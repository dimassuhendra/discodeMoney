<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\PencatatanInvestasi;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    /**
     * Hitung status budget Uang Makan untuk Kemarin, Hari Ini, dan Estimasi Besok
     */
    public function getUangMakanSummary(int $userId): array
    {
        $baseBudget = 40000;
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonthSoFar = $today->day;

        // 1. Ambil Sumber Dana Uang Makan Utama
        $sumberDanaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%uang makan%')
            ->where('nama', 'not like', '%sisa%')
            ->first();

        $sumberDanaId = $sumberDanaUangMakan ? $sumberDanaUangMakan->id : null;

        // 2. Ambil Sumber Dana "Sisa Uang Makan" (jika ada)
        $sumberDanaSisaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%sisa%')
            ->where('nama', 'like', '%uang makan%')
            ->first();

        // 3. Kalkulasi Rantai Defisit Berantai dari Tgl 1 s/d Besok
        $deficitsMap = $this->getDailyDeficits($userId, $sumberDanaId, $tomorrow);

        // Data Kemarin
        $dataKemarin = $deficitsMap[$yesterday->format('Y-m-d')] ?? ['budget_awal' => $baseBudget, 'potongan_defisit' => 0, 'pengeluaran' => 0];
        $pengeluaranKemarin = $dataKemarin['pengeluaran'];
        $defisitKemarin = $dataKemarin['potongan_defisit'];

        // Data Hari Ini
        $dataHariIni = $deficitsMap[$today->format('Y-m-d')] ?? ['budget_awal' => $baseBudget, 'potongan_defisit' => 0, 'pengeluaran' => 0];
        $budgetHariIniAwal = $dataHariIni['budget_awal'];
        $potonganDefisitKemarin = $dataHariIni['potongan_defisit'];
        $pengeluaranHariIni = $dataHariIni['pengeluaran'];
        $sisaBudgetHariIni = $budgetHariIniAwal - $pengeluaranHariIni;

        // Data Besok
        $dataBesok = $deficitsMap[$tomorrow->format('Y-m-d')] ?? ['budget_awal' => $baseBudget, 'potongan_defisit' => 0, 'pengeluaran' => 0];
        $budgetBesokAwal = $dataBesok['budget_awal'];
        $potonganDefisitHariIni = $dataBesok['potongan_defisit'];

        // 4. Perhitungan Month to Date (MTD)
        $totalBudgetMtd = $baseBudget * $daysInMonthSoFar;

        $totalPengeluaranMtd = $sumberDanaId ? Pengeluaran::where('user_id', $userId)
            ->where('sumber_dana_id', $sumberDanaId)
            ->whereBetween('tanggal', [$startOfMonth->format('Y-m-d'), $today->format('Y-m-d')])
            ->sum('jumlah') : 0;

        $totalPengeluaranSisaDirect = $sumberDanaSisaUangMakan ? Pengeluaran::where('user_id', $userId)
            ->where('sumber_dana_id', $sumberDanaSisaUangMakan->id)
            ->sum('jumlah') : 0;

        $totalPemasukanSisaDirect = $sumberDanaSisaUangMakan ? Pemasukan::where('user_id', $userId)
            ->where('sumber_dana_id', $sumberDanaSisaUangMakan->id)
            ->sum('jumlah') : 0;

        $sisaDanaMtdAwal = $totalBudgetMtd - $totalPengeluaranMtd;
        $sisaDanaMtd = (max(0, $sisaDanaMtdAwal) + $totalPemasukanSisaDirect) - $totalPengeluaranSisaDirect;

        return [
            'base_budget' => $baseBudget,
            'kemarin' => [
                'tanggal' => $yesterday->translatedFormat('d M Y'),
                'pengeluaran' => $pengeluaranKemarin,
                'defisit' => $defisitKemarin,
                'is_overbudget' => $pengeluaranKemarin > $dataKemarin['budget_awal'],
            ],
            'hari_ini' => [
                'tanggal' => $today->translatedFormat('d M Y'),
                'budget_awal' => $budgetHariIniAwal,
                'potongan_defisit_kemarin' => $potonganDefisitKemarin,
                'pengeluaran' => $pengeluaranHariIni,
                'sisa_budget' => $sisaBudgetHariIni,
                'is_overbudget' => $sisaBudgetHariIni < 0,
            ],
            'besok' => [
                'tanggal' => $tomorrow->translatedFormat('d M Y'),
                'estimasi_budget' => $budgetBesokAwal,
                'potongan_defisit_hari_ini' => $potonganDefisitHariIni,
            ],
            'month_to_date' => [
                'total_hari' => $daysInMonthSoFar,
                'total_budget' => $totalBudgetMtd,
                'total_pengeluaran' => $totalPengeluaranMtd + $totalPengeluaranSisaDirect,
                'sisa_dana' => $sisaDanaMtd,
            ],
        ];
    }

    /**
     * Data Chart Harian (7 Hari Minggu Ini)
     */
    public function getDailyChartData(int $userId): array
    {
        $startOfWeek = Carbon::now()->startOfWeek(); // Senin
        $endOfWeek = Carbon::now()->endOfWeek();     // Minggu

        $sumberDanaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%uang makan%')
            ->first();

        $sumberDanaId = $sumberDanaUangMakan ? $sumberDanaUangMakan->id : null;

        $labels = [];
        $dataPengeluaran = [];

        for ($date = $startOfWeek->clone(); $date->lte($endOfWeek); $date->addDay()) {
            $labels[] = $date->translatedFormat('D (d/M)');

            $total = $sumberDanaId ? Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaId)
                ->whereDate('tanggal', $date)
                ->sum('jumlah') : 0;

            $dataPengeluaran[] = (float) $total;
        }

        return [
            'labels' => $labels,
            'data' => $dataPengeluaran,
            'benchmark' => 40000,
        ];
    }

    /**
     * Data Chart Mingguan Bulan Ini (Minggu 1 s/d Minggu ke-4/5)
     */
    public function getWeeklyMonthlyChartData(int $userId): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $sumberDanaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%uang makan%')
            ->first();

        $sumberDanaId = $sumberDanaUangMakan ? $sumberDanaUangMakan->id : null;

        $labels = [];
        $dataPengeluaran = [];

        $currentStart = $startOfMonth->clone();
        $weekNumber = 1;

        while ($currentStart->lte($endOfMonth)) {
            $currentEnd = $currentStart->clone()->endOfWeek()->min($endOfMonth);

            $labels[] = "Minggu " . $weekNumber . " (" . $currentStart->format('d') . "-" . $currentEnd->format('d M') . ")";

            $total = $sumberDanaId ? Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaId)
                ->whereBetween('tanggal', [$currentStart->format('Y-m-d'), $currentEnd->format('Y-m-d')])
                ->sum('jumlah') : 0;

            $dataPengeluaran[] = (float) $total;

            $currentStart = $currentEnd->clone()->addDay();
            $weekNumber++;
        }

        return [
            'labels' => $labels,
            'data' => $dataPengeluaran,
        ];
    }

    /**
     * Hitung peta defisit harian berantai dari awal bulan sampai tanggal tujuan
     */
    protected function getDailyDeficits(int $userId, ?int $sumberDanaId, Carbon $untilDate): array
    {
        $baseBudget = 40000;
        $startOfMonth = $untilDate->clone()->startOfMonth();
        $currentDate = $startOfMonth->clone();

        // Ambil sekaligus pengeluaran harian dalam 1 query agar hemat database
        $dailyExpenses = [];
        if ($sumberDanaId) {
            $dailyExpenses = Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaId)
                ->whereBetween('tanggal', [$startOfMonth->format('Y-m-d'), $untilDate->format('Y-m-d')])
                ->selectRaw('DATE(tanggal) as date_only, SUM(jumlah) as total')
                ->groupBy('date_only')
                ->pluck('total', 'date_only')
                ->toArray();
        }

        $carriedDeficit = 0;
        $dailyDeficits = [];

        while ($currentDate->lte($untilDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $pengeluaranHariIni = $dailyExpenses[$dateStr] ?? 0;

            // Budget efektif hari ini adalah Base Budget dikurangi defisit kemarin
            $budgetHariIni = max(0, $baseBudget - $carriedDeficit);

            // Simpan defisit bawaan untuk hari ini
            $dailyDeficits[$dateStr] = [
                'budget_awal' => $budgetHariIni,
                'potongan_defisit' => $carriedDeficit,
                'pengeluaran' => $pengeluaranHariIni,
            ];

            // Hitung defisit yang akan dibawakan ke besok
            if ($pengeluaranHariIni > $budgetHariIni) {
                $carriedDeficit = $pengeluaranHariIni - $budgetHariIni;
            } else {
                // Jika hemat/surplus, defisit HANGUS (reset)
                $carriedDeficit = 0;
            }

            $currentDate->addDay();
        }

        return $dailyDeficits;
    }
}

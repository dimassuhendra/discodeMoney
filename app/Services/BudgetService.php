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

        $sumberDanaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%uang makan%')
            ->first();

        $sumberDanaId = $sumberDanaUangMakan ? $sumberDanaUangMakan->id : null;

        // 1. Pengeluaran Kemarin
        $pengeluaranKemarin = $sumberDanaId ? Pengeluaran::where('user_id', $userId)
            ->where('sumber_dana_id', $sumberDanaId)
            ->whereDate('tanggal', $yesterday)
            ->sum('jumlah') : 0;

        $defisitKemarin = max(0, $pengeluaranKemarin - $baseBudget);

        // 2. Pengeluaran Hari Ini & Sisa Budget Hari Ini
        $pengeluaranHariIni = $sumberDanaId ? Pengeluaran::where('user_id', $userId)
            ->where('sumber_dana_id', $sumberDanaId)
            ->whereDate('tanggal', $today)
            ->sum('jumlah') : 0;

        $budgetHariIniAwal = max(0, $baseBudget - $defisitKemarin);
        $sisaBudgetHariIni = $budgetHariIniAwal - $pengeluaranHariIni;
        $defisitHariIni = max(0, $pengeluaranHariIni - $budgetHariIniAwal);

        // 3. Estimasi Budget Besok
        $budgetBesokAwal = max(0, $baseBudget - $defisitHariIni);

        return [
            'base_budget' => $baseBudget,
            'kemarin' => [
                'tanggal' => $yesterday->translatedFormat('d M Y'),
                'pengeluaran' => $pengeluaranKemarin,
                'defisit' => $defisitKemarin,
                'is_overbudget' => $pengeluaranKemarin > $baseBudget,
            ],
            'hari_ini' => [
                'tanggal' => $today->translatedFormat('d M Y'),
                'budget_awal' => $budgetHariIniAwal,
                'potongan_defisit_kemarin' => $defisitKemarin,
                'pengeluaran' => $pengeluaranHariIni,
                'sisa_budget' => $sisaBudgetHariIni,
                'is_overbudget' => $sisaBudgetHariIni < 0,
            ],
            'besok' => [
                'tanggal' => Carbon::tomorrow()->translatedFormat('d M Y'),
                'estimasi_budget' => $budgetBesokAwal,
                'potongan_defisit_hari_ini' => $defisitHariIni,
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
}

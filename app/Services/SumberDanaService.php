<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SumberDanaService
{
    /**
     * Get list sumber dana dengan rincian kalkulasi saldo & statistik
     */
    public function getSumberDanaSummary(int $userId): Collection
    {
        $sumberDanaList = SumberDana::where('user_id', $userId)->get();

        // 1. Cari sumber dana Uang Makan Utama
        $sumberDanaUangMakan = $sumberDanaList->first(function ($sd) {
            return str_contains(strtolower($sd->nama), 'uang makan') && !str_contains(strtolower($sd->nama), 'sisa');
        });

        // 2. Hitung akumulasi MTD Uang Makan Utama
        $baseBudgetUangMakan = 40000;
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonthSoFar = $today->day;

        $totalBudgetMtd = $baseBudgetUangMakan * $daysInMonthSoFar;

        $totalPengeluaranUangMakanMtd = $sumberDanaUangMakan ? Pengeluaran::where('user_id', $userId)
            ->where('sumber_dana_id', $sumberDanaUangMakan->id)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->sum('jumlah') : 0;

        // Sisa akumulasi dari Uang Makan Utama
        $sisaDanaMtdUangMakan = $totalBudgetMtd - $totalPengeluaranUangMakanMtd;

        // 3. Transformasi seluruh Sumber Dana
        $sumberDanaList->transform(function ($sd) use ($sisaDanaMtdUangMakan, $totalBudgetMtd, $totalPengeluaranUangMakanMtd) {

            $isSisaUangMakan = str_contains(strtolower($sd->nama), 'sisa') && str_contains(strtolower($sd->nama), 'uang makan');

            if ($isSisaUangMakan) {
                // Hitung pengeluaran & pemasukan spesifik yang dicatat pada kantong "Sisa Uang Makan"
                $pengeluaranLangsung = Pengeluaran::where('sumber_dana_id', $sd->id)->sum('jumlah');
                $pemasukanLangsung = Pemasukan::where('sumber_dana_id', $sd->id)->sum('jumlah');

                // Total Alokasi = Sisa MTD Uang Makan Utama + Pemasukan Manual ke kantong ini
                $totalAlokasi = max(0, $sisaDanaMtdUangMakan) + $pemasukanLangsung;

                // Total Pengeluaran = Pengeluaran yang menggunakan ID "Sisa Uang Makan"
                $totalOut = $pengeluaranLangsung;
                $totalIn = $pemasukanLangsung;

                // Saldo Aktif = Alokasi Sisa MTD - Pengeluaran Langsung
                $saldoAktif = $totalAlokasi - $totalOut;

                if ($totalAlokasi > 0) {
                    $persentase = ($saldoAktif / $totalAlokasi) * 100;
                    $persentase = max(0, min(100, $persentase));
                } else {
                    $persentase = $saldoAktif > 0 ? 100 : 0;
                }
            } else {
                // Kalkulasi reguler untuk sumber dana biasa
                $totalIn = Pemasukan::where('sumber_dana_id', $sd->id)->sum('jumlah');
                $totalOut = Pengeluaran::where('sumber_dana_id', $sd->id)->sum('jumlah');

                $totalAlokasi = ($sd->budget ?? 0) + $totalIn;
                $saldoAktif = $totalAlokasi - $totalOut;

                if ($totalAlokasi > 0) {
                    $persentase = ($saldoAktif / $totalAlokasi) * 100;
                    $persentase = max(0, min(100, $persentase));
                } else {
                    $persentase = $saldoAktif > 0 ? 100 : 0;
                }
            }

            $sd->total_pemasukan = $totalIn;
            $sd->total_pengeluaran = $totalOut;
            $sd->total_alokasi = $totalAlokasi;
            $sd->saldo_aktif = $saldoAktif;
            $sd->persentase = round($persentase, 1);

            return $sd;
        });

        return $sumberDanaList;
    }

    public function createSumberDana(array $data, int $userId): SumberDana
    {
        return SumberDana::create([
            'user_id'       => $userId,
            'nama'          => $data['nama'],
            'budget'        => $data['budget'] ?? 0,
            'budget_harian' => $data['budget_harian'] ?? null,
        ]);
    }

    public function updateSumberDana(SumberDana $sumberDana, array $data): bool
    {
        return $sumberDana->update([
            'nama'          => $data['nama'],
            'budget'        => $data['budget'] ?? 0,
            'budget_harian' => $data['budget_harian'] ?? null,
        ]);
    }

    public function deleteSumberDana(SumberDana $sumberDana): bool
    {
        // Proteksi jika sudah memiliki transaksi pengeluaran/pemasukan
        $hasTransactions = Pengeluaran::where('sumber_dana_id', $sumberDana->id)->exists()
            || Pemasukan::where('sumber_dana_id', $sumberDana->id)->exists();

        if ($hasTransactions) {
            throw new \Exception("Sumber dana '{$sumberDana->nama}' memiliki riwayat transaksi dan tidak dapat dihapus.");
        }

        return $sumberDana->delete();
    }
}

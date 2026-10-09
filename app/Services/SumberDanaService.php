<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\SumberDana;
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

        $sumberDanaList->transform(function ($sd) {
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

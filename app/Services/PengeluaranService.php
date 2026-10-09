<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Pengeluaran;
use App\Models\PencatatanInvestasi;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengeluaranService
{
    /**
     * Get Insight & Statistik Pengeluaran Bulan Ini
     */
    public function getPengeluaranStats(int $userId): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysInMonthSoFar = Carbon::now()->day; // Jumlah hari yang telah berlalu bulan ini

        // 1. Total Pengeluaran Seluruh Sumber Dana Bulan Ini
        $totalPengeluaranBulanIni = Pengeluaran::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        // 2. Cari Sumber Dana Uang Makan
        $sumberDanaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%uang makan%')
            ->first();

        $sumberDanaIdUangMakan = $sumberDanaUangMakan ? $sumberDanaUangMakan->id : null;

        // 3. Statistik Khusus Uang Makan Bulan Ini
        $totalUangMakanBulanIni = 0;
        $avgUangMakanHarian = 0;
        $maxUangMakanDay = null;
        $minUangMakanDay = null;

        if ($sumberDanaIdUangMakan) {
            $totalUangMakanBulanIni = Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaIdUangMakan)
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('jumlah');

            $avgUangMakanHarian = $daysInMonthSoFar > 0 ? ($totalUangMakanBulanIni / $daysInMonthSoFar) : 0;

            // Agregasi harian Uang Makan bulan ini
            $dailyUangMakan = Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaIdUangMakan)
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->select('tanggal', DB::raw('SUM(jumlah) as total_harian'))
                ->groupBy('tanggal')
                ->orderBy('total_harian', 'desc')
                ->get();

            if ($dailyUangMakan->isNotEmpty()) {
                // Hari Pengeluaran Uang Makan Terbesar
                $maxItem = $dailyUangMakan->first();
                $maxUangMakanDay = [
                    'tanggal' => Carbon::parse($maxItem->tanggal)->translatedFormat('d M Y'),
                    'nominal' => $maxItem->total_harian,
                ];

                // Hari Pengeluaran Uang Makan Terkecil
                $minItem = $dailyUangMakan->last();
                $minUangMakanDay = [
                    'tanggal' => Carbon::parse($minItem->tanggal)->translatedFormat('d M Y'),
                    'nominal' => $minItem->total_harian,
                ];
            }
        }

        return [
            'total_pengeluaran' => $totalPengeluaranBulanIni,
            'uang_makan_stats' => [
                'total' => $totalUangMakanBulanIni,
                'avg_harian' => round($avgUangMakanHarian),
                'max_day' => $maxUangMakanDay,
                'min_day' => $minUangMakanDay,
            ],
        ];
    }

    /**
     * Store Pengeluaran + Otomatisasi Draft Investasi & Barang
     */
    public function createPengeluaran(array $data, int $userId): Pengeluaran
    {
        return DB::transaction(function () use ($data, $userId) {
            $pengeluaran = Pengeluaran::create([
                'user_id'        => $userId,
                'sumber_dana_id' => $data['sumber_dana_id'],
                'tanggal'        => $data['tanggal'],
                'keterangan'     => $data['keterangan'],
                'jumlah'         => $data['jumlah'],
            ]);

            $sumberDana = SumberDana::find($data['sumber_dana_id']);

            // Automatic Draft Investasi
            if ($sumberDana && strtolower($sumberDana->nama) === 'uang investasi') {
                PencatatanInvestasi::create([
                    'user_id'        => $userId,
                    'pengeluaran_id' => $pengeluaran->id,
                    'nama'           => $data['keterangan'],
                    'harga_beli_idr' => $data['jumlah'],
                    'tanggal_beli'   => $data['tanggal'],
                    'status'         => 'masih_dimiliki',
                ]);
            }

            // Automatic Barang / Aset
            if (!empty($data['is_barang']) && $data['is_barang'] == true) {
                Barang::create([
                    'user_id'        => $userId,
                    'pengeluaran_id' => $pengeluaran->id,
                    'nama_barang'    => $data['keterangan'],
                    'jenis_barang'   => $data['jenis_barang'] ?? 'barang_mati',
                    'harga'          => $data['jumlah'],
                    'tempat_beli'    => $data['tempat_beli'] ?? null,
                    'tanggal_beli'   => $data['tanggal'],
                ]);
            }

            return $pengeluaran;
        });
    }

    /**
     * Update Pengeluaran & Sinkronisasi Data Relasi
     */
    public function updatePengeluaran(Pengeluaran $pengeluaran, array $data): bool
    {
        return DB::transaction(function () use ($pengeluaran, $data) {
            $pengeluaran->update([
                'sumber_dana_id' => $data['sumber_dana_id'],
                'tanggal'        => $data['tanggal'],
                'keterangan'     => $data['keterangan'],
                'jumlah'         => $data['jumlah'],
            ]);

            // Sync ke Draft Investasi (jika ada)
            if ($pengeluaran->pencatatanInvestasi) {
                $pengeluaran->pencatatanInvestasi->update([
                    'nama'           => $data['keterangan'],
                    'harga_beli_idr' => $data['jumlah'],
                    'tanggal_beli'   => $data['tanggal'],
                ]);
            }

            // Sync ke Barang (jika ada)
            if ($pengeluaran->barang) {
                $pengeluaran->barang->update([
                    'nama_barang'  => $data['keterangan'],
                    'harga'        => $data['jumlah'],
                    'tanggal_beli' => $data['tanggal'],
                    'jenis_barang' => $data['jenis_barang'] ?? $pengeluaran->barang->jenis_barang,
                    'tempat_beli'  => $data['tempat_beli'] ?? $pengeluaran->barang->tempat_beli,
                ]);
            }

            return true;
        });
    }

    /**
     * Delete Pengeluaran (Cascade ke Draft Investasi & Barang)
     */
    public function deletePengeluaran(Pengeluaran $pengeluaran): bool
    {
        return DB::transaction(function () use ($pengeluaran) {
            if ($pengeluaran->pencatatanInvestasi) {
                $pengeluaran->pencatatanInvestasi->delete();
            }

            if ($pengeluaran->barang) {
                $pengeluaran->barang->delete();
            }

            return $pengeluaran->delete();
        });
    }
}

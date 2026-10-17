<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\PencatatanInvestasi;
use App\Models\Pengeluaran;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengeluaranService
{
    /**
     * Mengambil seluruh data halaman index pengeluaran (List, Sumber Dana, dan Stats yang terfilter)
     */
    public function getPengeluaranPageData(int $userId, array $filters = []): array
    {
        // 1. Penentuan Rentang Tanggal Filter
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // 2. Query List Pengeluaran
        $query = Pengeluaran::with(['sumberDana', 'pencatatanInvestasi', 'barang'])
            ->where('user_id', $userId);

        if (!empty($filters['search'])) {
            $query->where('keterangan', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['sumber_dana_id'])) {
            $query->where('sumber_dana_id', $filters['sumber_dana_id']);
        }

        $query->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $pengeluaranList = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15);

        // 3. Sumber Dana List
        $sumberDanaList = SumberDana::where('user_id', $userId)->get();

        // 4. Kalkulasi Statistik Dinamis Sesuai Periode Tanggal
        $stats = $this->calculatePengeluaranStats($userId, $startDate, $endDate);

        return compact('pengeluaranList', 'sumberDanaList', 'stats');
    }

    /**
     * Hitung Statistik Pengeluaran Berdasarkan Rentang Tanggal
     */
    protected function calculatePengeluaranStats(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');

        // Total Pengeluaran Seluruh Sumber Dana pada Periode
        $totalPengeluaran = Pengeluaran::where('user_id', $userId)
            ->whereBetween('tanggal', [$startDateStr, $endDateStr])
            ->sum('jumlah');

        // Cari ID Sumber Dana Uang Makan
        $sumberDanaUangMakan = SumberDana::where('user_id', $userId)
            ->where('nama', 'like', '%uang makan%')
            ->first();

        $totalUangMakan = 0;
        $avgUangMakanHarian = 0;
        $maxUangMakanDay = null;
        $minUangMakanDay = null;

        if ($sumberDanaUangMakan) {
            $sumberDanaIdUangMakan = $sumberDanaUangMakan->id;

            $totalUangMakan = Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaIdUangMakan)
                ->whereBetween('tanggal', [$startDateStr, $endDateStr])
                ->sum('jumlah');

            // Hitung total hari efektif dalam periode rentang tanggal (minimal 1 hari)
            $totalDays = max(1, $startDate->diffInDays($endDate) + 1);

            // Jika periode filter melampaui hari ini pada bulan berjalan, pembagi disesuaikan sampai hari ini
            if ($endDate->isCurrentMonth() && $endDate->isFuture()) {
                $totalDays = max(1, Carbon::now()->day);
            }

            $avgUangMakanHarian = $totalUangMakan / $totalDays;

            // Agregasi Harian Uang Makan
            $dailyUangMakan = Pengeluaran::where('user_id', $userId)
                ->where('sumber_dana_id', $sumberDanaIdUangMakan)
                ->whereBetween('tanggal', [$startDateStr, $endDateStr])
                ->select('tanggal', DB::raw('SUM(jumlah) as total_harian'))
                ->groupBy('tanggal')
                ->orderBy('total_harian', 'desc')
                ->get();

            if ($dailyUangMakan->isNotEmpty()) {
                $maxItem = $dailyUangMakan->first();
                $maxUangMakanDay = [
                    'tanggal' => Carbon::parse($maxItem->tanggal)->translatedFormat('d M Y'),
                    'nominal' => $maxItem->total_harian,
                ];

                $minItem = $dailyUangMakan->last();
                $minUangMakanDay = [
                    'tanggal' => Carbon::parse($minItem->tanggal)->translatedFormat('d M Y'),
                    'nominal' => $minItem->total_harian,
                ];
            }
        }

        $totalUangMakanSpec = Pengeluaran::where('user_id', $userId)
            ->whereHas('sumberDana', function ($q) {
                $q->where('nama', 'like', '%uang makan%');
            })
            ->whereBetween('tanggal', [$startDateStr, $endDateStr])
            ->sum('jumlah');

        $totalUangBulanan = Pengeluaran::where('user_id', $userId)
            ->whereHas('sumberDana', function ($q) {
                $q->where('nama', 'like', '%uang bulanan%');
            })
            ->whereBetween('tanggal', [$startDateStr, $endDateStr])
            ->sum('jumlah');

        $totalUangInvestasi = Pengeluaran::where('user_id', $userId)
            ->whereHas('sumberDana', function ($q) {
                $q->where('nama', 'like', '%uang investasi%');
            })
            ->whereBetween('tanggal', [$startDateStr, $endDateStr])
            ->sum('jumlah');

        return [
            'total_pengeluaran' => $totalPengeluaran,
            'total_uang_makan'     => $totalUangMakanSpec,
            'total_uang_bulanan'   => $totalUangBulanan,
            'total_uang_investasi' => $totalUangInvestasi,
            'uang_makan_stats'  => [
                'total'      => $totalUangMakan,
                'avg_harian' => round($avgUangMakanHarian),
                'max_day'    => $maxUangMakanDay,
                'min_day'    => $minUangMakanDay,
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

            if ($pengeluaran->pencatatanInvestasi) {
                $pengeluaran->pencatatanInvestasi->update([
                    'nama'           => $data['keterangan'],
                    'harga_beli_idr' => $data['jumlah'],
                    'tanggal_beli'   => $data['tanggal'],
                ]);
            }

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

<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PemasukanService
{
    /**
     * Hitung Statistik Top Cards & Bar Chart Pemasukan Bulanan
     */
    public function getPemasukanStats(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');
        $startOfYear = $now->copy()->startOfYear()->format('Y-m-d');

        // Base Query
        $query = Pemasukan::where('user_id', $userId);
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        $filteredList = $query->get();

        // 1. Top Cards Aggregations
        $pemasukanBulanIni = Pemasukan::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $totalPemasukanFiltered = $filteredList->sum('jumlah');

        // Rekening Penerima Terbesar
        $topSumberDana = Pemasukan::select('sumber_dana_id', DB::raw('SUM(jumlah) as total'))
            ->where('user_id', $userId)
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
            ->groupBy('sumber_dana_id')
            ->orderBy('total', 'desc')
            ->with('sumberDana')
            ->first();

        $pemasukanYTD = Pemasukan::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfYear, $now->format('Y-m-d')])
            ->sum('jumlah');

        // 2. Bar Chart: Monthly Trend (Aman untuk SQLite & MySQL)
        $driver = DB::connection()->getDriverName();
        $dateFormat = ($driver === 'sqlite') ? "strftime('%Y-%m', tanggal)" : "DATE_FORMAT(tanggal, '%Y-%m')";

        $monthlyQuery = Pemasukan::select(
            DB::raw("{$dateFormat} as year_month"),
            DB::raw('SUM(jumlah) as total_jumlah')
        )
            ->where('user_id', $userId);

        if ($startDate && $endDate) {
            $monthlyQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $monthlyData = $monthlyQuery->groupBy('year_month')
            ->orderBy('year_month', 'asc')
            ->get();

        $chartBar = [
            'labels' => $monthlyData->map(fn($m) => Carbon::parse($m->year_month . '-01')->translatedFormat('M Y')),
            'values' => $monthlyData->pluck('total_jumlah'),
        ];

        return [
            'bulan_ini'        => $pemasukanBulanIni,
            'total_filtered'   => $totalPemasukanFiltered,
            'top_sumber_dana'  => $topSumberDana ? $topSumberDana->sumberDana->nama ?? '-' : '-',
            'pemasukan_ytd'    => $pemasukanYTD,
            'chart_bar'        => $chartBar,
        ];
    }

    public function createPemasukan(array $data, int $userId): Pemasukan
    {
        return DB::transaction(function () use ($data, $userId) {
            return Pemasukan::create([
                'user_id'        => $userId,
                'sumber_dana_id' => $data['sumber_dana_id'],
                'keterangan'     => $data['keterangan'],
                'jumlah'         => $data['jumlah'],
                'tanggal'        => $data['tanggal'],
            ]);
        });
    }

    public function updatePemasukan(Pemasukan $pemasukan, array $data): bool
    {
        return DB::transaction(function () use ($pemasukan, $data) {
            return $pemasukan->update([
                'sumber_dana_id' => $data['sumber_dana_id'],
                'keterangan'     => $data['keterangan'],
                'jumlah'         => $data['jumlah'],
                'tanggal'        => $data['tanggal'],
            ]);
        });
    }

    public function deletePemasukan(Pemasukan $pemasukan): bool
    {
        return $pemasukan->delete();
    }
}

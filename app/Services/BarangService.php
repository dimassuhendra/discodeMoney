<?php

namespace App\Services;

use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BarangService
{
    /**
     * Hitung Statistik Top Cards, Insight Analytics & Chart Data Inventaris
     */
    public function getBarangStats(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Barang::where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_beli', [$startDate, $endDate]);
        }

        $allBarang = $query->get();

        // 1. Filter Kategori
        $barangMati = $allBarang->where('jenis_barang', 'barang_mati');
        $barangHidup = $allBarang->where('jenis_barang', 'barang_hidup');
        $kebutuhanRutin = $allBarang->where('jenis_barang', 'kebutuhan_rutin');

        // Aggregations
        $totalNilai = $barangMati->sum('harga') + $barangHidup->sum('harga');
        $totalCount = $barangMati->count() + $barangHidup->count();

        // 2. Metrics Baru (Avg, Max, Min)
        $avgBarangMati = $barangMati->count() > 0 ? $barangMati->avg('harga') : 0;
        $avgBarangHidup = $barangHidup->count() > 0 ? $barangHidup->avg('harga') : 0;

        $maxBarangHidup = $barangHidup->sortByDesc('harga')->first();
        $minBarangMati = $barangMati->sortByDesc('harga')->first();

        // 3. Chart Pie (Distribution)
        $chartPie = [
            'barang_mati'      => $barangMati->sum('harga'),
            'barang_hidup'     => $barangHidup->sum('harga'),
            'kebutuhan_rutin'  => $kebutuhanRutin->sum('harga'),
        ];

        // 4. Trend Growth Data (Bulanan & Tahunan)
        $driver = DB::connection()->getDriverName();
        $formatMonth = $driver === 'sqlite' ? "strftime('%Y-%m', tanggal_beli)" : "DATE_FORMAT(tanggal_beli, '%Y-%m')";
        $formatYear  = $driver === 'sqlite' ? "strftime('%Y', tanggal_beli)" : "DATE_FORMAT(tanggal_beli, '%Y')";

        // --- A. Query Growth Monthly ---
        $monthlyRaw = Barang::select(
            DB::raw("{$formatMonth} as period"),
            'jenis_barang',
            DB::raw('SUM(harga) as total_harga')
        )
            ->where('user_id', $userId)
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('tanggal_beli', [$startDate, $endDate]))
            ->groupBy('period', 'jenis_barang')
            ->orderBy('period', 'asc')
            ->get();

        $months = $monthlyRaw->pluck('period')->unique()->values()->all();

        $chartGrowthMonthly = [
            'labels'      => array_map(fn($m) => Carbon::parse($m . '-01')->translatedFormat('M Y'), $months),
            'barang_mati' => [],
            'barang_hidup' => [],
            'total_aset'  => [],
        ];

        // Kumulatif akumulasi bulanan
        $cumMati = 0;
        $cumHidup = 0;
        foreach ($months as $m) {
            $cumMati += $monthlyRaw->where('period', $m)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $cumHidup += $monthlyRaw->where('period', $m)->where('jenis_barang', 'barang_hidup')->sum('total_harga');

            $chartGrowthMonthly['barang_mati'][] = $cumMati;
            $chartGrowthMonthly['barang_hidup'][] = $cumHidup;
            $chartGrowthMonthly['total_aset'][]  = $cumMati + $cumHidup;
        }

        // --- B. Query Growth Yearly ---
        $yearlyRaw = Barang::select(
            DB::raw("{$formatYear} as period"),
            'jenis_barang',
            DB::raw('SUM(harga) as total_harga')
        )
            ->where('user_id', $userId)
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('tanggal_beli', [$startDate, $endDate]))
            ->groupBy('period', 'jenis_barang')
            ->orderBy('period', 'asc')
            ->get();

        $years = $yearlyRaw->pluck('period')->unique()->values()->all();

        $chartGrowthYearly = [
            'labels'      => $years,
            'barang_mati' => [],
            'barang_hidup' => [],
            'total_aset'  => [],
        ];

        $cumMatiYr = 0;
        $cumHidupYr = 0;
        foreach ($years as $y) {
            $cumMatiYr += $yearlyRaw->where('period', $y)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $cumHidupYr += $yearlyRaw->where('period', $y)->where('jenis_barang', 'barang_hidup')->sum('total_harga');

            $chartGrowthYearly['barang_mati'][] = $cumMatiYr;
            $chartGrowthYearly['barang_hidup'][] = $cumHidupYr;
            $chartGrowthYearly['total_aset'][]  = $cumMatiYr + $cumHidupYr;
        }

        return [
            'total_nilai'          => $totalNilai,
            'total_count'          => $totalCount,
            'mati'                 => ['count' => $barangMati->count(), 'total' => $barangMati->sum('harga')],
            'hidup'                => ['count' => $barangHidup->count(), 'total' => $barangHidup->sum('harga')],
            'rutin'                => ['count' => $kebutuhanRutin->count(), 'total' => $kebutuhanRutin->sum('harga')],
            'avg_barang_mati'      => $avgBarangMati,
            'avg_barang_hidup'     => $avgBarangHidup,
            'max_barang_hidup'     => $maxBarangHidup,
            'min_barang_mati'      => $minBarangMati,
            'chart_pie'            => $chartPie,
            'growth_monthly'       => $chartGrowthMonthly,
            'growth_yearly'        => $chartGrowthYearly,
        ];
    }

    public function getItemHistory(int $userId, string $namaBarang): array
    {
        $history = Barang::where('user_id', $userId)
            ->where('nama_barang', $namaBarang)
            ->orderBy('tanggal_beli', 'desc')
            ->get();

        $totalDibeli = $history->count();
        $totalPengeluaran = $history->sum('harga');

        $bulanUnik = $history->map(fn($item) => Carbon::parse($item->tanggal_beli)->format('Y-m'))->unique()->count();
        $rataRataBulanan = $bulanUnik > 0 ? $totalPengeluaran / $bulanUnik : $totalPengeluaran;

        return [
            'nama_barang'       => $namaBarang,
            'total_dibeli'      => $totalDibeli,
            'total_pengeluaran' => $totalPengeluaran,
            'rata_rata_bulanan' => $rataRataBulanan,
            'history_list'      => $history->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'tanggal_beli' => Carbon::parse($item->tanggal_beli)->format('d/m/Y'),
                    'harga'        => $item->harga,
                    'tempat_beli'  => $item->tempat_beli ?? '-',
                    'jenis_barang' => $item->jenis_barang,
                ];
            }),
        ];
    }

    public function createBarang(array $data, int $userId): Barang
    {
        return Barang::create([
            'user_id'        => $userId,
            'pengeluaran_id' => null,
            'nama_barang'    => $data['nama_barang'],
            'jenis_barang'   => $data['jenis_barang'],
            'harga'          => $data['harga'],
            'tempat_beli'    => $data['tempat_beli'] ?? null,
            'tanggal_beli'   => $data['tanggal_beli'],
        ]);
    }

    public function updateBarang(Barang $barang, array $data): bool
    {
        return DB::transaction(function () use ($barang, $data) {
            $barang->update([
                'nama_barang'  => $data['nama_barang'],
                'jenis_barang' => $data['jenis_barang'],
                'harga'        => $data['harga'],
                'tempat_beli'  => $data['tempat_beli'] ?? null,
                'tanggal_beli' => $data['tanggal_beli'],
            ]);

            if ($barang->pengeluaran) {
                $barang->pengeluaran->update([
                    'keterangan' => $data['nama_barang'],
                    'jumlah'     => $data['harga'],
                    'tanggal'    => $data['tanggal_beli'],
                ]);
            }

            return true;
        });
    }

    public function deleteBarang(Barang $barang): bool
    {
        return $barang->delete();
    }
}

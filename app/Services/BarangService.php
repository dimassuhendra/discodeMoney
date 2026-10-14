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

        // 1. Filter Kategori (Operasional, Produktif, Kebutuhan Rutin)
        $barangOperasional = $allBarang->where('jenis_barang', 'barang_mati');
        $barangProduktif   = $allBarang->where('jenis_barang', 'barang_hidup');
        $kebutuhanRutin    = $allBarang->where('jenis_barang', 'kebutuhan_rutin');

        // Aggregations Utama (Total Nilai Inventaris = Operasional + Produktif)
        $totalNilai = $barangOperasional->sum('harga') + $barangProduktif->sum('harga');
        $totalCount = $barangOperasional->count() + $barangProduktif->count();

        // 2. Metrics Insight Cards (Avg & Max)
        $avgBarangOperasional = $barangOperasional->count() > 0 ? $barangOperasional->avg('harga') : 0;
        $avgBarangProduktif   = $barangProduktif->count() > 0 ? $barangProduktif->avg('harga') : 0;

        $maxBarangProduktif   = $barangProduktif->sortByDesc('harga')->first();
        $maxBarangOperasional = $barangOperasional->sortByDesc('harga')->first();

        // 3. Donut Chart 1: Persentase Aset yang Dimiliki (All Time - Barang Operasional & Produktif saja)
        $chartPieAset = [
            'barang_operasional' => $barangOperasional->sum('harga'),
            'barang_produktif'   => $barangProduktif->sum('harga'),
        ];

        // 4. Donut Chart 2: Persentase Pembelian Bulan Ini / Month to Date (MTD - 3 Jenis Barang)
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $mtdBarang = Barang::where('user_id', $userId)
            ->whereBetween('tanggal_beli', [$startOfMonth, $endOfMonth])
            ->get();

        $chartPieMtd = [
            'barang_operasional' => $mtdBarang->where('jenis_barang', 'barang_mati')->sum('harga'),
            'barang_produktif'   => $mtdBarang->where('jenis_barang', 'barang_hidup')->sum('harga'),
            'kebutuhan_rutin'    => $mtdBarang->where('jenis_barang', 'kebutuhan_rutin')->sum('harga'),
        ];

        // 5. Trend Growth Data (Bulanan & Tahunan)
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
            'labels'             => array_map(fn($m) => Carbon::parse($m . '-01')->translatedFormat('M Y'), $months),
            'barang_operasional' => [],
            'barang_produktif'   => [],
            'kebutuhan_rutin'    => [],
            'total_inventaris'   => [],
        ];

        // Kumulatif akumulasi bulanan
        $cumOperasional = 0;
        $cumProduktif   = 0;
        $cumRutin       = 0;
        foreach ($months as $m) {
            $cumOperasional += $monthlyRaw->where('period', $m)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $cumProduktif   += $monthlyRaw->where('period', $m)->where('jenis_barang', 'barang_hidup')->sum('total_harga');
            $cumRutin       += $monthlyRaw->where('period', $m)->where('jenis_barang', 'kebutuhan_rutin')->sum('total_harga');

            $chartGrowthMonthly['barang_operasional'][] = $cumOperasional;
            $chartGrowthMonthly['barang_produktif'][]   = $cumProduktif;
            $chartGrowthMonthly['kebutuhan_rutin'][]    = $cumRutin;
            $chartGrowthMonthly['total_inventaris'][]   = $cumOperasional + $cumProduktif;
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
            'labels'             => $years,
            'barang_operasional' => [],
            'barang_produktif'   => [],
            'kebutuhan_rutin'    => [],
            'total_inventaris'   => [],
        ];

        $cumOperasionalYr = 0;
        $cumProduktifYr   = 0;
        $cumRutinYr       = 0;
        foreach ($years as $y) {
            $cumOperasionalYr += $yearlyRaw->where('period', $y)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $cumProduktifYr   += $yearlyRaw->where('period', $y)->where('jenis_barang', 'barang_hidup')->sum('total_harga');
            $cumRutinYr       += $yearlyRaw->where('period', $y)->where('jenis_barang', 'kebutuhan_rutin')->sum('total_harga');

            $chartGrowthYearly['barang_operasional'][] = $cumOperasionalYr;
            $chartGrowthYearly['barang_produktif'][]   = $cumProduktifYr;
            $chartGrowthYearly['kebutuhan_rutin'][]    = $cumRutinYr;
            $chartGrowthYearly['total_inventaris'][]   = $cumOperasionalYr + $cumProduktifYr;
        }

        return [
            'total_nilai'            => $totalNilai,
            'total_count'            => $totalCount,
            'operasional'            => ['count' => $barangOperasional->count(), 'total' => $barangOperasional->sum('harga')],
            'produktif'              => ['count' => $barangProduktif->count(), 'total' => $barangProduktif->sum('harga')],
            'rutin'                  => ['count' => $kebutuhanRutin->count(), 'total' => $kebutuhanRutin->sum('harga')],
            'avg_barang_operasional' => $avgBarangOperasional,
            'avg_barang_produktif'   => $avgBarangProduktif,
            'max_barang_produktif'   => $maxBarangProduktif,
            'max_barang_operasional' => $maxBarangOperasional,
            'chart_pie_aset'         => $chartPieAset,
            'chart_pie_mtd'          => $chartPieMtd,
            'growth_monthly'         => $chartGrowthMonthly,
            'growth_yearly'          => $chartGrowthYearly,
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

<?php

namespace App\Services;

use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BarangService
{
    /**
     * Hitung Statistik Top Cards, Insight Analytics & Chart Data Inventaris (7 Line Charts + Bar & Donut)
     */
    public function getBarangStats(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Barang::where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_beli', [$startDate, $endDate]);
        }

        $allBarang = $query->get();

        // 1. Filter Kategori
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

        // 3. Kalkulasi MTD & Comparison Kebutuhan Rutin (Bulan Ini vs Bulan Lalu)
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth   = $now->copy()->endOfMonth()->format('Y-m-d');

        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');

        // Nominal Rutin MTD Bulan Ini
        $rutinMtdCurrent = Barang::where('user_id', $userId)
            ->where('jenis_barang', 'kebutuhan_rutin')
            ->whereBetween('tanggal_beli', [$startOfMonth, $endOfMonth])
            ->sum('harga');

        // Nominal Rutin Bulan Lalu
        $rutinLastMonth = Barang::where('user_id', $userId)
            ->where('jenis_barang', 'kebutuhan_rutin')
            ->whereBetween('tanggal_beli', [$startOfLastMonth, $endOfLastMonth])
            ->sum('harga');

        // Item Rutin Paling Sering Dibeli & Insight Baris ke-4
        $topRutinItem = Barang::select('nama_barang', DB::raw('COUNT(*) as total_beli'), DB::raw('SUM(harga) as total_spent'))
            ->where('user_id', $userId)
            ->where('jenis_barang', 'kebutuhan_rutin')
            ->groupBy('nama_barang')
            ->orderBy('total_beli', 'desc')
            ->first();

        $rutinDiffNominal = $rutinMtdCurrent - $rutinLastMonth;
        $rutinPercentage  = $this->calculatePercentageChange($rutinLastMonth, $rutinMtdCurrent);

        // 4. Donut Chart 1 & 2
        $chartPieAset = [
            'barang_operasional' => $barangOperasional->sum('harga'),
            'barang_produktif'   => $barangProduktif->sum('harga'),
        ];

        $mtdBarang = Barang::where('user_id', $userId)
            ->whereBetween('tanggal_beli', [$startOfMonth, $endOfMonth])
            ->get();

        $chartPieMtd = [
            'barang_operasional' => $mtdBarang->where('jenis_barang', 'barang_mati')->sum('harga'),
            'barang_produktif'   => $mtdBarang->where('jenis_barang', 'barang_hidup')->sum('harga'),
            'kebutuhan_rutin'    => $mtdBarang->where('jenis_barang', 'kebutuhan_rutin')->sum('harga'),
        ];

        // 5. Query Agregasi Bulanan & Tahunan
        $driver = DB::connection()->getDriverName();
        $formatMonth = $driver === 'sqlite' ? "strftime('%Y-%m', tanggal_beli)" : "DATE_FORMAT(tanggal_beli, '%Y-%m')";
        $formatYear  = $driver === 'sqlite' ? "strftime('%Y', tanggal_beli)" : "DATE_FORMAT(tanggal_beli, '%Y')";

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

        // Dynamic Datasets untuk 7 Chart (Monthly)
        $chartPurchasesMonthly = [
            'labels'             => array_map(fn($m) => Carbon::parse($m . '-01')->translatedFormat('M Y'), $months),
            'barang_operasional' => [],
            'barang_produktif'   => [],
            'kebutuhan_rutin'    => [],
            'total_pembelian'    => [],
        ];

        $chartGrowthMonthly = [
            'labels'             => array_map(fn($m) => Carbon::parse($m . '-01')->translatedFormat('M Y'), $months),
            'barang_operasional' => [],
            'barang_produktif'   => [],
            'total_inventaris'   => [],
        ];

        $cumOperasional = 0;
        $cumProduktif   = 0;

        foreach ($months as $m) {
            $sumOp    = (float) $monthlyRaw->where('period', $m)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $sumProd  = (float) $monthlyRaw->where('period', $m)->where('jenis_barang', 'barang_hidup')->sum('total_harga');
            $sumRutin = (float) $monthlyRaw->where('period', $m)->where('jenis_barang', 'kebutuhan_rutin')->sum('total_harga');

            // Pure Monthly Sum (Kas Pembelian Bulan Terkait)
            $chartPurchasesMonthly['barang_operasional'][] = $sumOp;
            $chartPurchasesMonthly['barang_produktif'][]   = $sumProd;
            $chartPurchasesMonthly['kebutuhan_rutin'][]    = $sumRutin;
            $chartPurchasesMonthly['total_pembelian'][]    = $sumOp + $sumProd + $sumRutin;

            // Accumulated Growth Total (Running Total Pertumbuhan Aset)
            $cumOperasional += $sumOp;
            $cumProduktif   += $sumProd;

            $chartGrowthMonthly['barang_operasional'][] = $cumOperasional;
            $chartGrowthMonthly['barang_produktif'][]   = $cumProduktif;
            $chartGrowthMonthly['total_inventaris'][]   = $cumOperasional + $cumProduktif;
        }

        // Calculation Growth Indicators (% Comparison vs Last Month)
        $indicators = $this->calculateGrowthIndicators($chartGrowthMonthly, $chartPurchasesMonthly);

        // --- Query Agregasi Yearly ---
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
            'total_inventaris'   => [],
        ];

        $chartPurchasesYearly = [
            'labels'             => $years,
            'barang_operasional' => [],
            'barang_produktif'   => [],
            'kebutuhan_rutin'    => [],
            'total_pembelian'    => [],
        ];

        $cumOperasionalYr = 0;
        $cumProduktifYr   = 0;
        foreach ($years as $y) {
            $yOp    = (float) $yearlyRaw->where('period', $y)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $yProd  = (float) $yearlyRaw->where('period', $y)->where('jenis_barang', 'barang_hidup')->sum('total_harga');
            $yRutin = (float) $yearlyRaw->where('period', $y)->where('jenis_barang', 'kebutuhan_rutin')->sum('total_harga');

            $chartPurchasesYearly['barang_operasional'][] = $yOp;
            $chartPurchasesYearly['barang_produktif'][]   = $yProd;
            $chartPurchasesYearly['kebutuhan_rutin'][]    = $yRutin;
            $chartPurchasesYearly['total_pembelian'][]    = $yOp + $yProd + $yRutin;

            $cumOperasionalYr += $yOp;
            $cumProduktifYr   += $yProd;

            $chartGrowthYearly['barang_operasional'][] = $cumOperasionalYr;
            $chartGrowthYearly['barang_produktif'][]   = $cumProduktifYr;
            $chartGrowthYearly['total_inventaris'][]   = $cumOperasionalYr + $cumProduktifYr;
        }

        return [
            'total_nilai'            => $totalNilai,
            'total_count'            => $totalCount,
            'operasional'            => ['count' => $barangOperasional->count(), 'total' => $barangOperasional->sum('harga')],
            'produktif'              => ['count' => $barangProduktif->count(), 'total' => $barangProduktif->sum('harga')],
            'rutin_mtd'              => [
                'count'        => $kebutuhanRutin->count(),
                'total_mtd'    => $rutinMtdCurrent,
                'last_month'   => $rutinLastMonth,
                'diff_nominal' => $rutinDiffNominal,
                'percentage'   => $rutinPercentage,
                'top_item'     => $topRutinItem,
            ],
            'avg_barang_operasional' => $avgBarangOperasional,
            'avg_barang_produktif'   => $avgBarangProduktif,
            'max_barang_produktif'   => $maxBarangProduktif,
            'max_barang_operasional' => $maxBarangOperasional,
            'chart_pie_aset'         => $chartPieAset,
            'chart_pie_mtd'          => $chartPieMtd,
            'growth_monthly'         => $chartGrowthMonthly,
            'growth_yearly'          => $chartGrowthYearly,
            'purchases_monthly'      => $chartPurchasesMonthly,
            'purchases_yearly'       => $chartPurchasesYearly,
            'indicators'             => $indicators,
        ];
    }

    /**
     * Helper Perhitungan Persentase Perubahan
     */
    protected function calculatePercentageChange(float $previous, float $current): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Helper Indikator Persentase untuk ke-7 Chart
     */
    protected function calculateGrowthIndicators(array $growthMonthly, array $purchasesMonthly): array
    {
        $getPrevAndCurrent = function (array $arr) {
            $count = count($arr);
            if ($count < 2) {
                return ['prev' => 0, 'curr' => $count === 1 ? $arr[0] : 0];
            }
            return ['prev' => $arr[$count - 2], 'curr' => $arr[$count - 1]];
        };

        // Indikator Pertumbuhan Aset (Stock)
        $prodGrowth = $getPrevAndCurrent($growthMonthly['barang_produktif']);
        $opGrowth   = $getPrevAndCurrent($growthMonthly['barang_operasional']);
        $totGrowth  = $getPrevAndCurrent($growthMonthly['total_inventaris']);

        // Indikator Pembelian MTD (Flow)
        $prodPur    = $getPrevAndCurrent($purchasesMonthly['barang_produktif']);
        $opPur      = $getPrevAndCurrent($purchasesMonthly['barang_operasional']);
        $rutinPur   = $getPrevAndCurrent($purchasesMonthly['kebutuhan_rutin']);
        $totPur     = $getPrevAndCurrent($purchasesMonthly['total_pembelian']);

        return [
            'growth_total'       => $this->calculatePercentageChange($totGrowth['prev'], $totGrowth['curr']),
            'growth_produktif'   => $this->calculatePercentageChange($prodGrowth['prev'], $prodGrowth['curr']),
            'growth_operasional' => $this->calculatePercentageChange($opGrowth['prev'], $opGrowth['curr']),

            'purchase_total'     => $this->calculatePercentageChange($totPur['prev'], $totPur['curr']),
            'purchase_produktif' => $this->calculatePercentageChange($prodPur['prev'], $prodPur['curr']),
            'purchase_operasional' => $this->calculatePercentageChange($opPur['prev'], $opPur['curr']),
            'purchase_rutin'     => $this->calculatePercentageChange($rutinPur['prev'], $rutinPur['curr']),
        ];
    }

    /**
     * History Item + Eager Loading Relasi Pengeluaran
     */
    public function getItemHistory(int $userId, string $namaBarang): array
    {
        $history = Barang::with('pengeluaran')
            ->where('user_id', $userId)
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
                    'kebutuhan'    => $item->pengeluaran ? $item->pengeluaran->kebutuhan : '-',
                    'keterangan'   => $item->pengeluaran ? ($item->pengeluaran->keterangan ?? '-') : '-',
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
                    'kebutuhan'  => $data['nama_barang'],
                    'keterangan' => $data['keterangan'] ?? null,
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

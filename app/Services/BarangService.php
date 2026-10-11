<?php

namespace App\Services;

use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BarangService
{
    /**
     * Hitung Statistik Top Cards & Chart Data Inventaris
     */
    public function getBarangStats(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Barang::where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_beli', [$startDate, $endDate]);
        }

        $allBarang = $query->get();

        // 1. Top Cards Aggregations
        $totalNilai = $allBarang->sum('harga');
        $totalCount = $allBarang->count();

        $barangMati = $allBarang->where('jenis_barang', 'barang_mati');
        $barangHidup = $allBarang->where('jenis_barang', 'barang_hidup');
        $kebutuhanRutin = $allBarang->where('jenis_barang', 'kebutuhan_rutin');

        // 2. Chart 1: Distribution Data (Doughnut/Pie)
        $chartPie = [
            'barang_mati'     => $barangMati->sum('harga'),
            'barang_hidup'    => $barangHidup->sum('harga'),
            'kebutuhan_rutin' => $kebutuhanRutin->sum('harga'),
        ];

        // 3. Chart 2: Grouped Bar Chart Trend Monthly
        $driver = DB::connection()->getDriverName();

        // Gunakan sintaks yang sesuai dengan Database Driver
        if ($driver === 'sqlite') {
            $dateFormat = "strftime('%Y-%m', tanggal_beli)";
        } else {
            $dateFormat = "DATE_FORMAT(tanggal_beli, '%Y-%m')";
        }

        $monthlyQuery = Barang::select(
            DB::raw("{$dateFormat} as year_month"),
            'jenis_barang',
            DB::raw('SUM(harga) as total_harga')
        )
            ->where('user_id', $userId);

        if ($startDate && $endDate) {
            $monthlyQuery->whereBetween('tanggal_beli', [$startDate, $endDate]);
        }

        $monthlyData = $monthlyQuery->groupBy('year_month', 'jenis_barang')
            ->orderBy('year_month', 'asc')
            ->get();

        $months = $monthlyData->pluck('year_month')->unique()->values()->all();
        $chartBar = [
            'labels'          => array_map(fn($m) => Carbon::parse($m . '-01')->translatedFormat('M Y'), $months),
            'barang_mati'     => [],
            'barang_hidup'    => [],
            'kebutuhan_rutin' => [],
            'totals'          => [],
        ];

        foreach ($months as $m) {
            $matiVal = $monthlyData->where('year_month', $m)->where('jenis_barang', 'barang_mati')->sum('total_harga');
            $hidupVal = $monthlyData->where('year_month', $m)->where('jenis_barang', 'barang_hidup')->sum('total_harga');
            $rutinVal = $monthlyData->where('year_month', $m)->where('jenis_barang', 'kebutuhan_rutin')->sum('total_harga');

            $chartBar['barang_mati'][] = $matiVal;
            $chartBar['barang_hidup'][] = $hidupVal;
            $chartBar['kebutuhan_rutin'][] = $rutinVal;
            $chartBar['totals'][] = $matiVal + $hidupVal + $rutinVal;
        }

        return [
            'total_nilai'            => $totalNilai,
            'total_count'            => $totalCount,
            'mati'                   => ['count' => $barangMati->count(), 'total' => $barangMati->sum('harga')],
            'hidup'                  => ['count' => $barangHidup->count(), 'total' => $barangHidup->sum('harga')],
            'rutin'                  => ['count' => $kebutuhanRutin->count(), 'total' => $kebutuhanRutin->sum('harga')],
            'chart_pie'              => $chartPie,
            'chart_bar'              => $chartBar,
        ];
    }

    /**
     * Agregasi Histori Item per Nama Barang (Opsi A)
     */
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

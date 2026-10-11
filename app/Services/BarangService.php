<?php

namespace App\Services;

use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BarangService
{
    /**
     * Hitung Statistik Top Cards Inventaris Barang
     */
    public function getBarangStats(int $userId): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $allBarang = Barang::where('user_id', $userId)->get();

        $totalNilaiBarang = $allBarang->sum('harga');
        $totalBarangMati = $allBarang->where('jenis_barang', 'barang_mati')->count();
        $totalBarangHidup = $allBarang->where('jenis_barang', 'barang_hidup')->count();

        $pembelianBulanIni = Barang::where('user_id', $userId)
            ->whereBetween('tanggal_beli', [$startOfMonth, $endOfMonth])
            ->sum('harga');

        return [
            'total_nilai'         => $totalNilaiBarang,
            'count_barang_mati'  => $totalBarangMati,
            'count_barang_hidup' => $totalBarangHidup,
            'pembelian_bulan_ini' => $pembelianBulanIni,
        ];
    }

    /**
     * Create Barang Manual (Tanpa Pengeluaran Kas Baru)
     */
    public function createBarang(array $data, int $userId): Barang
    {
        return Barang::create([
            'user_id'        => $userId,
            'pengeluaran_id' => null, // Manual input
            'nama_barang'    => $data['nama_barang'],
            'jenis_barang'   => $data['jenis_barang'],
            'harga'          => $data['harga'],
            'tempat_beli'    => $data['tempat_beli'] ?? null,
            'tanggal_beli'   => $data['tanggal_beli'],
        ]);
    }

    /**
     * Update Barang & Sinkronisasi ke Pengeluaran (jika terikat)
     */
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

            // Sync balik ke tabel Pengeluaran jika barang ini terhubung ke transaksi pengeluaran
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

    /**
     * Delete Barang
     */
    public function deleteBarang(Barang $barang): bool
    {
        return DB::transaction(function () use ($barang) {
            // Note: Hapus barang tidak menghapus pengeluaran agar history transaksi kas tetap utuh,
            // tetapi melepas ikatan (foreign key nullified / dikelola terpisah).
            return $barang->delete();
        });
    }
}

<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\PencatatanInvestasi;
use App\Models\SumberDana;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Simpan Pengeluaran Harian & Otomatisasi ke Investasi/Barang
     */
    public function storePengeluaran(array $data, int $userId): Pengeluaran
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Simpan Pengeluaran Utama
            $pengeluaran = Pengeluaran::create([
                'user_id'        => $userId,
                'sumber_dana_id' => $data['sumber_dana_id'],
                'tanggal'        => $data['tanggal'],
                'keterangan'     => $data['keterangan'],
                'jumlah'         => $data['jumlah'],
            ]);

            // Cek nama sumber dana yang dipilih
            $sumberDana = SumberDana::find($data['sumber_dana_id']);

            // 2. Logika Otomatisasi Investasi (Draft State)
            if ($sumberDana && strtolower($sumberDana->nama) === 'uang investasi') {
                PencatatanInvestasi::create([
                    'user_id'        => $userId,
                    'pengeluaran_id' => $pengeluaran->id,
                    'nama'           => $data['keterangan'], // Nama sementara dari keterangan
                    'harga_beli_idr' => $data['jumlah'],     // Total rupiah real yang keluar
                    'tanggal_beli'   => $data['tanggal'],
                    'status'         => 'masih_dimiliki',
                ]);
            }

            // 3. Logika Otomatisasi Barang/Aset
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
     * Simpan Pemasukan Harian
     */
    public function storePemasukan(array $data, int $userId): Pemasukan
    {
        return Pemasukan::create([
            'user_id'        => $userId,
            'sumber_dana_id' => $data['sumber_dana_id'] ?? null,
            'tanggal'        => $data['tanggal'],
            'keterangan'     => $data['keterangan'],
            'jumlah'         => $data['jumlah'],
        ]);
    }
}

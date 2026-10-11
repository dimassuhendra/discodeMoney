<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\PencatatanInvestasi;
use App\Models\Pengeluaran;
use App\Models\SumberDana;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PengaturanService
{
    /**
     * Hitung Informasi Status Akun & Total Record DB
     */
    public function getAccountStats(User $user): array
    {
        $userId = $user->id;

        $countPemasukan = Pemasukan::where('user_id', $userId)->count();
        $countPengeluaran = Pengeluaran::where('user_id', $userId)->count();
        $countInvestasi = PencatatanInvestasi::where('user_id', $userId)->count();
        $countBarang = Barang::where('user_id', $userId)->count();
        $countSumberDana = SumberDana::where('user_id', $userId)->count();

        return [
            'joined_at'          => $user->created_at->translatedFormat('d F Y'),
            'total_pemasukan'    => $countPemasukan,
            'total_pengeluaran'  => $countPengeluaran,
            'total_investasi'    => $countInvestasi,
            'total_barang'       => $countBarang,
            'total_sumber_dana'  => $countSumberDana,
            'grand_total_record' => $countPemasukan + $countPengeluaran + $countInvestasi + $countBarang,
            'app_version'        => 'v1.0.0 (Minggu, 11 Oktober 2026)',
        ];
    }

    /**
     * Update Email User
     */
    public function updateEmail(User $user, string $email): bool
    {
        return $user->update(['email' => $email]);
    }

    /**
     * Update Kode Akses / Password
     */
    public function updateKodeAkses(User $user, string $kodeAksesLama, string $kodeAksesBaru): bool
    {
        if (!Hash::check($kodeAksesLama, $user->kode_akses)) {
            throw new \Exception('Kode akses saat ini tidak sesuai.');
        }

        return $user->update([
            'kode_akses' => Hash::make($kodeAksesBaru),
        ]);
    }

    /**
     * Export Seluruh Data Transaksi ke Format CSV
     */
    public function exportDataCsv(int $userId): string
    {
        $pemasukan = Pemasukan::where('user_id', $userId)->get();
        $pengeluaran = Pengeluaran::where('user_id', $userId)->get();
        $investasi = PencatatanInvestasi::where('user_id', $userId)->get();
        $barang = Barang::where('user_id', $userId)->get();

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Modul/Tipe', 'ID', 'Tanggal', 'Keterangan/Nama', 'Nominal/Harga (IDR)', 'Status/Jenis']);

        foreach ($pemasukan as $p) {
            fputcsv($output, ['Pemasukan', $p->id, $p->tanggal, $p->keterangan, $p->jumlah, '-']);
        }

        foreach ($pengeluaran as $p) {
            fputcsv($output, ['Pengeluaran', $p->id, $p->tanggal, $p->keterangan, $p->jumlah, '-']);
        }

        foreach ($investasi as $i) {
            fputcsv($output, ['Investasi', $i->id, $i->tanggal_beli, $i->nama, $i->harga_beli_idr, $i->status]);
        }

        foreach ($barang as $b) {
            fputcsv($output, ['Inventaris Barang', $b->id, $b->tanggal_beli, $b->nama_barang, $b->harga, $b->jenis_barang]);
        }

        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);

        return $csvData;
    }

    /**
     * Reset Seluruh Data Transaksi (Danger Zone)
     */
    public function resetAllData(User $user, string $kodeAksesKonfirmasi): bool
    {
        if (!Hash::check($kodeAksesKonfirmasi, $user->kode_akses)) {
            throw new \Exception('Konfirmasi kode akses salah. Reset data dibatalkan.');
        }

        return DB::transaction(function () use ($user) {
            $userId = $user->id;

            PencatatanInvestasi::where('user_id', $userId)->delete();
            Barang::where('user_id', $userId)->delete();
            Pengeluaran::where('user_id', $userId)->delete();
            Pemasukan::where('user_id', $userId)->delete();

            // Reset Saldo Sumber Dana Kembali ke 0
            SumberDana::where('user_id', $userId)->update(['saldo' => 0]);

            return true;
        });
    }
}

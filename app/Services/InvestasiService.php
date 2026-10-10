<?php

namespace App\Services;

use App\Models\PencatatanInvestasi;
use Carbon\Carbon;

class InvestasiService
{
    public function getInvestasiStats(int $userId): array
    {
        $holdingAssets = PencatatanInvestasi::where('user_id', $userId)
            ->where('status', 'masih_dimiliki')
            ->get();

        $soldAssets = PencatatanInvestasi::where('user_id', $userId)
            ->where('status', 'sudah_dijual')
            ->get();

        // Total Nilai Aset Aktif Saat Ini (Holding)
        $totalHoldingValue = $holdingAssets->sum('harga_beli_idr');

        // Breakdown Top Cards Per Jenis Investasi
        $jenisList = ['Saham', 'USD', 'JPY', 'Reksadana'];
        $breakdownPerJenis = [];

        foreach ($jenisList as $jenis) {
            $sum = PencatatanInvestasi::where('user_id', $userId)
                ->where('status', 'masih_dimiliki')
                ->where('jenis_investasi', $jenis)
                ->sum('harga_beli_idr');

            $breakdownPerJenis[$jenis] = $sum;
        }

        // Durasi Hold Terlama (Aktif)
        $longestHoldingAsset = null;
        if ($holdingAssets->isNotEmpty()) {
            $now = Carbon::now();
            $longestHoldingAsset = $holdingAssets->map(function ($asset) use ($now) {
                $days = Carbon::parse($asset->tanggal_beli)->diffInDays($now);
                $asset->holding_days = $days;
                return $asset;
            })->sortByDesc('holding_days')->first();
        }

        // Durasi Sold Tercepat
        $fastestSoldAsset = null;
        if ($soldAssets->isNotEmpty()) {
            $fastestSoldAsset = $soldAssets->map(function ($asset) {
                $tglBeli = Carbon::parse($asset->tanggal_beli);
                $tglJual = $asset->tanggal_jual ? Carbon::parse($asset->tanggal_jual) : $tglBeli;
                $days = $tglBeli->diffInDays($tglJual);
                $asset->holding_days = max(1, $days);
                return $asset;
            })->sortBy('holding_days')->first();
        }

        return [
            'total_holding'         => $totalHoldingValue,
            'breakdown_jenis'       => $breakdownPerJenis,
            'longest_holding_asset' => $longestHoldingAsset,
            'fastest_sold_asset'    => $fastestSoldAsset,
        ];
    }

    public function createInvestasi(array $data, int $userId): PencatatanInvestasi
    {
        return PencatatanInvestasi::create([
            'user_id'         => $userId,
            'nama'            => $data['nama'],
            'detail'          => $data['detail'] ?? null,
            'jenis_investasi' => $data['jenis_investasi'],
            'platform'        => $data['platform'] ?? null,
            'harga_beli'      => $data['harga_beli'] ?? null,
            'harga_jual'      => $data['harga_jual'] ?? null,
            'harga_beli_idr'  => $data['harga_beli_idr'] ?? null,
            'harga_jual_idr'  => $data['harga_jual_idr'] ?? null,
            'tanggal_beli'    => $data['tanggal_beli'],
            'tanggal_jual'    => $data['tanggal_jual'] ?? null,
            'status'          => $data['status'],
        ]);
    }

    public function jualInvestasi(PencatatanInvestasi $investasi, array $data): bool
    {
        return $investasi->update([
            'tanggal_jual'   => $data['tanggal_jual'],
            'harga_jual'     => $data['harga_jual'] ?? $investasi->harga_jual,
            'harga_jual_idr' => $data['harga_jual_idr'] ?? $investasi->harga_jual_idr,
            'status'         => 'sudah_dijual',
        ]);
    }

    public function updateInvestasi(PencatatanInvestasi $investasi, array $data): bool
    {
        return $investasi->update([
            'nama'            => $data['nama'],
            'detail'          => $data['detail'] ?? null,
            'jenis_investasi' => $data['jenis_investasi'],
            'platform'        => $data['platform'] ?? null,
            'harga_beli'      => $data['harga_beli'] ?? null,
            'harga_jual'      => $data['harga_jual'] ?? null,
            'harga_beli_idr'  => $data['harga_beli_idr'] ?? null,
            'harga_jual_idr'  => $data['harga_jual_idr'] ?? null,
            'tanggal_beli'    => $data['tanggal_beli'],
            'tanggal_jual'    => $data['tanggal_jual'] ?? null,
            'status'          => $data['status'],
        ]);
    }

    public function deleteInvestasi(PencatatanInvestasi $investasi): bool
    {
        return $investasi->delete();
    }
}

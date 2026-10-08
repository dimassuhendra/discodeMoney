<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencatatanInvestasi extends Model
{
    use HasFactory;

    protected $table = 'pencatatan_investasi';

    protected $fillable = [
        'user_id',
        'pengeluaran_id',
        'nama',
        'jenis_investasi',
        'platform',
        'harga_beli',
        'harga_jual',
        'harga_beli_idr',
        'harga_jual_idr',
        'tanggal_beli',
        'tanggal_jual',
        'status',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:4',
        'harga_jual' => 'decimal:4',
        'harga_beli_idr' => 'decimal:2',
        'harga_jual_idr' => 'decimal:2',
        'tanggal_beli' => 'date',
        'tanggal_jual' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id');
    }

    /**
     * Accessor: Hitung Keuntungan/Kerugian dalam Rupiah
     */
    public function getGainLossIdrAttribute()
    {
        if ($this->status === 'sudah_dijual' && $this->harga_jual_idr && $this->harga_beli_idr) {
            return $this->harga_jual_idr - $this->harga_beli_idr;
        }

        return null;
    }

    /**
     * Accessor: Hitung ROI (Return on Investment) dalam Persentase (%)
     */
    public function getRoiPercentageAttribute()
    {
        if ($this->status === 'sudah_dijual' && $this->harga_jual_idr && $this->harga_beli_idr && $this->harga_beli_idr > 0) {
            return (($this->harga_jual_idr - $this->harga_beli_idr) / $this->harga_beli_idr) * 100;
        }

        return null;
    }
}

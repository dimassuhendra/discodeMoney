<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'user_id',
        'sumber_dana_id',
        'tanggal',
        'keterangan',
        'jumlah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class);
    }

    /**
     * Relasi ke Pencatatan Investasi (1 to 1 jika pengeluaran ini berkaitan dengan investasi)
     */
    public function pencatatanInvestasi()
    {
        return $this->hasOne(PencatatanInvestasi::class, 'pengeluaran_id');
    }

    /**
     * Relasi ke Barang (1 to 1 jika pengeluaran ini dicatat sebagai aset/barang)
     */
    public function barang()
    {
        return $this->hasOne(Barang::class, 'pengeluaran_id');
    }
}

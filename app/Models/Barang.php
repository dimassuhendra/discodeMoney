<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'user_id',
        'pengeluaran_id',
        'nama_barang',
        'jenis_barang',
        'harga',
        'tempat_beli',
        'tanggal_beli',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'tanggal_beli' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'kode_akses',
    ];

    protected $hidden = [
        'kode_akses',
        'remember_token',
    ];

    protected $casts = [
        'kode_akses' => 'hashed',
    ];

    /**
     * Relasi ke Sumber Dana
     */
    public function sumberDana()
    {
        return $this->hasMany(SumberDana::class);
    }

    /**
     * Relasi ke Pengeluaran
     */
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    /**
     * Relasi ke Pemasukan
     */
    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class);
    }

    /**
     * Relasi ke Pencatatan Investasi
     */
    public function pencatatanInvestasi()
    {
        return $this->hasMany(PencatatanInvestasi::class);
    }

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
}

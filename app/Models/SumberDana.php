<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    use HasFactory;

    protected $table = 'sumber_dana';

    protected $fillable = [
        'user_id',
        'nama',
        'budget',
        'budget_harian',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'budget_harian' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class);
    }
}

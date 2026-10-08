<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SumberDana;
use App\MOdels\Pemasukan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'email' => 'dimassuhendra0104@gmail.com',
            'kode_akses' => Hash::make('190702'), // Menggunakan Hash::make()
        ]);

        SumberDana::create([
            'user_id' => 1,
            'nama' => 'Uang Makan',
            'budget' => 1200000,
            'budget_harian' => 40000,
        ]);
        SumberDana::create([
            'user_id' => 1,
            'nama' => 'Uang Bulanan',
            'budget' => 1000000,
        ]);
        SumberDana::create([
            'user_id' => 1,
            'nama' => 'Uang Investasi',
            'budget' => 1000000,
        ]);

        Pemasukan::create([
            'user_id' => 1,
            'tanggal' => now(),
            'keterangan' => 'Gaji',
            'jumlah' => 3260000,
        ]);


    }
}

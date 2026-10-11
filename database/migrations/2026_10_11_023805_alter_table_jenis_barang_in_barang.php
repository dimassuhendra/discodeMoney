<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->enum('jenis_barang', ['barang_mati', 'barang_hidup', 'kebutuhan_rutin'])
                ->default('barang_mati')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->enum('jenis_barang', ['barang_mati', 'barang_hidup'])
                ->default('barang_mati')
                ->change();
        });
    }
};

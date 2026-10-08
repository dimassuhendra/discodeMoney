<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pengeluaran_id')->nullable()->constrained('pengeluaran')->nullOnDelete();

            $table->string('nama_barang');
            $table->enum('jenis_barang', ['barang_mati', 'barang_hidup'])->default('barang_mati');
            $table->decimal('harga', 15, 2);
            $table->string('tempat_beli')->nullable();
            $table->date('tanggal_beli');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

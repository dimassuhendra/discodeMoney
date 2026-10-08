<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencatatan_investasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pengeluaran_id')->nullable()->constrained('pengeluaran')->nullOnDelete();

            $table->string('nama'); // Contoh: BBCA, USD/IDR, BTC
            $table->string('jenis_investasi')->nullable(); // Saham, Forex, Crypto, Reksadana, dll.
            $table->string('platform')->nullable(); // Stockbit, Ajaib, Bibit, Indodax, dll.

            // Harga per unit (Opsional, untuk Saham/Crypto)
            $table->decimal('harga_beli', 20, 4)->nullable();
            $table->decimal('harga_jual', 20, 4)->nullable();

            // Real Total Nominal dalam Rupiah
            $table->decimal('harga_beli_idr', 15, 2)->nullable();
            $table->decimal('harga_jual_idr', 15, 2)->nullable();

            $table->date('tanggal_beli');
            $table->date('tanggal_jual')->nullable();

            $table->enum('status', ['masih_dimiliki', 'sudah_dijual'])->default('masih_dimiliki');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencatatan_investasi');
    }
};

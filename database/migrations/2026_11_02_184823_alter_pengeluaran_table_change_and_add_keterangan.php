<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->renameColumn('keterangan', 'kebutuhan');
            $table->string('keterangan')->nullable()->after('kebutuhan');
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            // Hapus kolom keterangan baru
            $table->dropColumn('keterangan');

            // Kembalikan nama kebutuhan menjadi keterangan
            $table->renameColumn('kebutuhan', 'keterangan');
        });
    }
};

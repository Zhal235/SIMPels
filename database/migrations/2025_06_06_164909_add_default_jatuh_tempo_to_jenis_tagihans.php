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
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            // Untuk tagihan insidental, digunakan untuk memilih tanggal jatuh tempo
            $table->integer('tanggal_jatuh_tempo')->nullable()->comment('Tanggal jatuh tempo untuk insidental (1-31)');
            $table->integer('bulan_jatuh_tempo')->nullable()->comment('Bulan jatuh tempo untuk insidental (0-11, 0=sama dengan bulan tagihan)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->dropColumn('tanggal_jatuh_tempo');
            $table->dropColumn('bulan_jatuh_tempo');
        });
    }
};

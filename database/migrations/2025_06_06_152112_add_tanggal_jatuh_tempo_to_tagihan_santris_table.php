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
        Schema::table('tagihan_santris', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable()->after('keterangan');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_santris', function (Blueprint $table) {
            $table->dropIndex(['tanggal_jatuh_tempo']);
            $table->dropColumn('tanggal_jatuh_tempo');
        });
    }
};

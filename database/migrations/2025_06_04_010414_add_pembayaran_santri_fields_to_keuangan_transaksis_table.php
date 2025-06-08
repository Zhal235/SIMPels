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
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            $table->unsignedBigInteger('pembayaran_santri_id')->nullable()->after('jenis_tagihan_id');
            $table->tinyInteger('bulan')->nullable()->after('tanggal'); // 1-12 untuk bulan
            
            $table->index('pembayaran_santri_id');
            $table->foreign('pembayaran_santri_id')->references('id')->on('pembayaran_santris')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_santri_id']);
            $table->dropIndex(['pembayaran_santri_id']);
            $table->dropColumn(['pembayaran_santri_id', 'bulan']);
        });
    }
};

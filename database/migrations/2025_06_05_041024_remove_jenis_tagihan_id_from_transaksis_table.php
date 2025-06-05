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
            // Drop foreign key pembayaran_santri_id dulu (jika ada)
            if (Schema::hasColumn('keuangan_transaksis', 'pembayaran_santri_id')) {
                $table->dropForeign(['pembayaran_santri_id']);
                $table->dropColumn('pembayaran_santri_id');
            }
        });
        
        // Dalam tabel terpisah, ubah tagihan_santri_id menjadi not null
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            // Drop foreign key tagihan_santri_id dulu
            $table->dropForeign(['tagihan_santri_id']);
            
            // Ubah menjadi not null
            $table->unsignedBigInteger('tagihan_santri_id')->nullable(false)->change();
            
            // Tambahkan kembali foreign key
            $table->foreign('tagihan_santri_id')->references('id')->on('tagihan_santris')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            // Kembalikan pembayaran_santri_id
            $table->unsignedBigInteger('pembayaran_santri_id')->after('santri_id')->nullable();
            $table->foreign('pembayaran_santri_id')->references('id')->on('pembayaran_santris')->onDelete('cascade');
            
            // Kembalikan tagihan_santri_id ke nullable
            $table->unsignedBigInteger('tagihan_santri_id')->nullable()->change();
        });
    }
};

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
            $table->unsignedBigInteger('tagihan_santri_id')->nullable()->after('santri_id');
            $table->index('tagihan_santri_id');
            $table->foreign('tagihan_santri_id')->references('id')->on('tagihan_santris')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            $table->dropForeign(['tagihan_santri_id']);
            $table->dropIndex(['tagihan_santri_id']);
            $table->dropColumn('tagihan_santri_id');
        });
    }
};

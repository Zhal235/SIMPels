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
        // Add tahun_ajaran_id to keuangan_transaksis table
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('keuangan_transaksis', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
                $table->index('tahun_ajaran_id');
            }
        });

        // Add tahun_ajaran_id to jenis_tagihans table  
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_tagihans', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
                $table->index('tahun_ajaran_id');
            }
        });

        // Add tahun_ajaran_id to keuangan_kategoris table
        Schema::table('keuangan_kategoris', function (Blueprint $table) {
            if (!Schema::hasColumn('keuangan_kategoris', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
                $table->index('tahun_ajaran_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraints and tahun_ajaran_id from keuangan_transaksis table
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropForeign(['santri_id']);
            $table->dropForeign(['jenis_pembayaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });

        // Remove foreign key constraint from jenis_pembayarans table
        Schema::table('jenis_pembayarans', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
        });

        Schema::table('keuangan_kategoris', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};

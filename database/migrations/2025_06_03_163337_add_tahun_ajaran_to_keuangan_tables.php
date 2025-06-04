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
        // Add tahun_ajaran_id to keuangan_transaksis table and foreign key constraints
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('keuangan_transaksis', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
                $table->index('tahun_ajaran_id');
            } else {
                // Just add foreign key constraint if column exists
                $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
            }
            
            // Add foreign key constraints for existing columns
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
            $table->foreign('jenis_pembayaran_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
        });

        // Add foreign key constraint to jenis_pembayarans table (column already exists)
        Schema::table('jenis_pembayarans', function (Blueprint $table) {
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
        });

        Schema::table('keuangan_kategoris', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('cascade');
            $table->index('tahun_ajaran_id');
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

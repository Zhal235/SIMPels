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
            // Drop existing index and foreign key if they exist
            $table->dropIndex(['jenis_pembayaran_id']);
            
            // Rename column
            $table->renameColumn('jenis_pembayaran_id', 'jenis_tagihan_id');
        });
        
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            // Add new index
            $table->index('jenis_tagihan_id');
            
            // Add foreign key constraint
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            // Drop foreign key and index
            $table->dropForeign(['jenis_tagihan_id']);
            $table->dropIndex(['jenis_tagihan_id']);
            
            // Rename column back
            $table->renameColumn('jenis_tagihan_id', 'jenis_pembayaran_id');
        });
        
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            // Add back the old index
            $table->index('jenis_pembayaran_id');
        });
    }
};

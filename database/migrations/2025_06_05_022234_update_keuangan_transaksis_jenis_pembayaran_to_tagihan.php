<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the old column exists and new column doesn't exist
        if (Schema::hasColumn('keuangan_transaksis', 'jenis_pembayaran_id') && 
            !Schema::hasColumn('keuangan_transaksis', 'jenis_tagihan_id')) {
            
            Schema::table('keuangan_transaksis', function (Blueprint $table) {
                // Drop existing index and foreign key if they exist
                try {
                    $table->dropIndex(['jenis_pembayaran_id']);
                } catch (\Exception $e) {
                    // Index may not exist, continue
                }
                
                // Add new column with same type
                $table->unsignedBigInteger('jenis_tagihan_id')->after('jenis_pembayaran_id');
            });
            
            // Copy data from old column to new column
            DB::statement('UPDATE keuangan_transaksis SET jenis_tagihan_id = jenis_pembayaran_id');
            
            Schema::table('keuangan_transaksis', function (Blueprint $table) {
                // Drop old column
                $table->dropColumn('jenis_pembayaran_id');
                
                // Add new index
                $table->index('jenis_tagihan_id');
                
                // Add foreign key constraint
                $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
            });
        }
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

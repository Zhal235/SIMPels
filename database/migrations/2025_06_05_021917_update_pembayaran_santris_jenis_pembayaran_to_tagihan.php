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
        if (Schema::hasColumn('pembayaran_santris', 'jenis_pembayaran_id') && 
            !Schema::hasColumn('pembayaran_santris', 'jenis_tagihan_id')) {
            
            Schema::table('pembayaran_santris', function (Blueprint $table) {
                // Drop foreign key constraint first if it exists
                try {
                    $table->dropForeign(['jenis_pembayaran_id']);
                } catch (\Exception $e) {
                    // Foreign key may not exist, continue
                }
                
                // Add new column with same type
                $table->unsignedBigInteger('jenis_tagihan_id')->after('jenis_pembayaran_id');
            });
            
            // Copy data from old column to new column
            DB::statement('UPDATE pembayaran_santris SET jenis_tagihan_id = jenis_pembayaran_id');
            
            Schema::table('pembayaran_santris', function (Blueprint $table) {
                // Drop old column
                $table->dropColumn('jenis_pembayaran_id');
                
                // Add new foreign key constraint
                $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_santris', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['jenis_tagihan_id']);
            
            // Rename column back
            $table->renameColumn('jenis_tagihan_id', 'jenis_pembayaran_id');
        });
        
        Schema::table('pembayaran_santris', function (Blueprint $table) {
            // Add back old foreign key constraint (will fail since jenis_pembayarans table doesn't exist)
            // $table->foreign('jenis_pembayaran_id')->references('id')->on('jenis_pembayarans')->onDelete('cascade');
        });
    }
};

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
        Schema::table('pembayaran_santris', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['jenis_pembayaran_id']);
            
            // Rename column
            $table->renameColumn('jenis_pembayaran_id', 'jenis_tagihan_id');
        });
        
        Schema::table('pembayaran_santris', function (Blueprint $table) {
            // Add new foreign key constraint
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
        });
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

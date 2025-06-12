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
        Schema::table('pegawais', function (Blueprint $table) {
            // Tambah kolom jabatan_id sebagai foreign key
            $table->unsignedBigInteger('jabatan_id')->nullable()->after('jabatan');
            
            // Tambah foreign key constraint
            $table->foreign('jabatan_id')->references('id')->on('jabatans')->onDelete('set null');
            
            // Tambah index untuk performance
            $table->index('jabatan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Drop foreign key dan index
            $table->dropForeign(['jabatan_id']);
            $table->dropIndex(['jabatan_id']);
            $table->dropColumn('jabatan_id');
        });
    }
};

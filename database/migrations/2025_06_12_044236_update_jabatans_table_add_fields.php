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
        Schema::table('jabatans', function (Blueprint $table) {
            $table->enum('kategori_jabatan', ['pengasuh', 'pengurus', 'pimpinan', 'naib', 'kepala', 'staff'])->after('level_jabatan');
            $table->unsignedBigInteger('bidang_id')->nullable()->after('kategori_jabatan');
            $table->unsignedBigInteger('parent_jabatan_id')->nullable()->after('bidang_id'); // untuk hierarki
            $table->boolean('is_struktural')->default(true)->after('parent_jabatan_id'); // struktural vs fungsional
            
            // Index
            $table->index(['kategori_jabatan', 'level_jabatan']);
            $table->index('bidang_id');
            
            // Foreign key
            $table->foreign('bidang_id')->references('id')->on('bidangs')->onDelete('set null');
            $table->foreign('parent_jabatan_id')->references('id')->on('jabatans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropForeign(['bidang_id']);
            $table->dropForeign(['parent_jabatan_id']);
            $table->dropColumn(['kategori_jabatan', 'bidang_id', 'parent_jabatan_id', 'is_struktural']);
        });
    }
};

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
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahun_ajaran');
            $table->year('tahun_mulai');
            $table->year('tahun_selesai');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index('is_active');
            $table->index(['tahun_mulai', 'tahun_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
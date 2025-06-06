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
        Schema::create('pembayaran_santris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->decimal('nominal_tagihan', 15, 2);
            $table->decimal('nominal_dibayar', 15, 2)->default(0);
            $table->json('bulan_pembayaran')->nullable()->comment('Array bulan untuk pembayaran bulanan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['santri_id', 'jenis_tagihan_id', 'tahun_ajaran_id'], 'idx_pembayaran_santri_main');
            $table->index('status');

            // Foreign keys
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_santris');
    }
};

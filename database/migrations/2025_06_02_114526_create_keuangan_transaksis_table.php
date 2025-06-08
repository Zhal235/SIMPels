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
        Schema::create('keuangan_transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->index('santri_id');
            $table->index('jenis_tagihan_id');
            $table->enum('tipe_pembayaran', ['sekali_bayar', 'cicilan', 'bulanan', 'tahunan']);
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
            $table->index('tahun_ajaran_id');
            
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_transaksis');
    }
};
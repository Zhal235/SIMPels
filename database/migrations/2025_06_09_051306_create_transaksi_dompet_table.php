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
        Schema::create('transaksi_dompet', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->unsignedBigInteger('dompet_id');
            $table->unsignedBigInteger('dompet_tujuan_id')->nullable(); // untuk transfer
            $table->enum('jenis_transaksi', ['top_up', 'pembelian', 'transfer_masuk', 'transfer_keluar', 'penarikan']);
            $table->string('kategori');
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('referensi_eksternal')->nullable(); // untuk integrasi dengan EPOS
            $table->unsignedBigInteger('transaksi_kas_id')->nullable(); // relasi ke transaksi kas
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamp('tanggal_transaksi');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('dompet_id')->references('id')->on('dompet');
            $table->foreign('dompet_tujuan_id')->references('id')->on('dompet');
            $table->foreign('transaksi_kas_id')->references('id')->on('transaksi_kas');
            $table->foreign('created_by')->references('id')->on('users');
            
            // Index
            $table->index(['dompet_id', 'tanggal_transaksi']);
            $table->index('kode_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_dompet');
    }
};

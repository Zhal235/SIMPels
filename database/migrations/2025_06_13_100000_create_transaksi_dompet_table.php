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
            $table->unsignedBigInteger('dompet_id');
            $table->enum('jenis_transaksi', [
                'topup', 'pembelian', 'pembayaran', 'transfer_masuk', 
                'transfer_keluar', 'refund', 'koreksi'
            ]);
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->string('referensi')->unique();
            $table->string('epos_transaction_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('dompet_id')->references('id')->on('dompet')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['dompet_id', 'jenis_transaksi']);
            $table->index('status');
            $table->index('created_at');
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

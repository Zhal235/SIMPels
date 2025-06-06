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
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_kas_id')->constrained('buku_kas')->onDelete('restrict');
            $table->foreignId('buku_kas_tujuan_id')->nullable()->constrained('buku_kas')->onDelete('restrict');
            $table->string('jenis_transaksi'); // pemasukan, pengeluaran, transfer
            $table->string('kategori')->nullable();
            $table->string('kode_transaksi')->unique();
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('metode_pembayaran')->nullable(); // kas, transfer bank, dll
            $table->string('no_referensi')->nullable(); // nomor cek, nomor transfer, dll
            $table->date('tanggal_transaksi');
            $table->string('bukti_transaksi')->nullable(); // path ke file bukti transaksi
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('tagihan_santri_id')->nullable()->constrained('tagihan_santris')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
    }
};

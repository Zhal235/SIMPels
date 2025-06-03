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
        Schema::create('keuangan_jenis_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: SPP, Uang Gedung
            $table->enum('kategori_pembayaran', ['Rutin', 'Insidental']);
            $table->decimal('nominal_tagihan', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_jenis_pembayarans');
    }
};

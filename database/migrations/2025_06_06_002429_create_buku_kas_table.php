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
        Schema::create('buku_kas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kas')->unique();
            $table->string('kode_kas')->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('jenis_kas', ['Operasional', 'Pembangunan', 'SPP', 'PSB', 'Insidental', 'Lainnya'])->default('Operasional');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_saat_ini', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_kas');
    }
};

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
        Schema::create('jenis_bayar', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis_bayar');
            $table->enum('klasifikasi', ['biaya_rutin_bulanan', 'biaya_incidential']);
            $table->foreignId('jenis_kas_id')->nullable()->constrained('jenis_kas')->onDelete('set null');
            // $table->decimal('nominal', 15, 2)->default(0); // Kolom nominal bisa dipertimbangkan lagi jika memang diperlukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_bayar');
    }
};
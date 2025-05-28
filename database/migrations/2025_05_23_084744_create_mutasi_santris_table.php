<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutasiSantrisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mutasi_santris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');          // relasi ke data santri
            $table->string('nama');
            $table->string('kelas_terakhir')->nullable();
            $table->string('asrama_terakhir')->nullable();
            $table->string('alasan');
            $table->string('tujuan_mutasi');                  // <--- Tambahan kolom tujuan mutasi
            $table->date('tanggal_mutasi');
            $table->timestamps();

            // Foreign key opsional
            // $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_santris');
    }
}

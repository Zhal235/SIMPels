<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('asrama_anggota', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('santri_id');
        $table->unsignedBigInteger('asrama_id');
        $table->date('tanggal_masuk')->nullable();
        $table->date('tanggal_keluar')->nullable();
        $table->timestamps();

        // Foreign key (optional, bisa juga tidak pakai)
        // $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        // $table->foreign('asrama_id')->references('id')->on('asramas')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asrama_anggota');
    }
};

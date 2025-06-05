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
        Schema::create('jenis_tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->decimal('nominal', 15, 2)->nullable();
            $table->boolean('is_bulanan')->default(false);
            $table->json('bulan_pembayaran')->nullable();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            $table->enum('kategori_tagihan', ['Rutin', 'Insidental'])->default('Rutin');
            $table->timestamps();

            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_tagihans');
    }
};
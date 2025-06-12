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
        Schema::create('bidangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang');
            $table->string('kode_bidang', 10)->unique();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('naib_penanggung_jawab_id')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->integer('urutan')->default(1); // untuk sorting
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Index
            $table->index(['status', 'urutan']);
            $table->index('nama_bidang');
            
            // Foreign key
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('naib_penanggung_jawab_id')->references('id')->on('pegawais');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidangs');
    }
};

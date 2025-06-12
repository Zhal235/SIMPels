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
        Schema::create('pegawai_jabatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id');
            $table->unsignedBigInteger('jabatan_id');
            $table->boolean('is_jabatan_utama')->default(false); // jabatan utama/primer
            $table->date('tanggal_mulai'); // mulai menjabat
            $table->date('tanggal_selesai')->nullable(); // selesai menjabat (null = masih aktif)
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            $table->foreign('jabatan_id')->references('id')->on('jabatans')->onDelete('cascade');
            
            // Indexes
            $table->index(['pegawai_id', 'is_jabatan_utama']);
            $table->index(['pegawai_id', 'status']);
            $table->unique(['pegawai_id', 'jabatan_id', 'tanggal_mulai'], 'unique_pegawai_jabatan_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_jabatan');
    }
};

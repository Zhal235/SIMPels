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
        Schema::create('perizinan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');
            $table->enum('jenis_izin', ['sakit', 'pulang', 'keluar', 'kegiatan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan');
            $table->string('bukti')->nullable(); // Path to uploaded document/image
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('alasan_ditolak')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};

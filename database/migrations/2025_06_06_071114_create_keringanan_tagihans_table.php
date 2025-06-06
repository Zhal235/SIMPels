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
        Schema::create('keringanan_tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->foreignId('jenis_tagihan_id')->nullable()->constrained('jenis_tagihans')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->enum('jenis_keringanan', ['potongan_persen', 'potongan_nominal', 'pembebasan', 'bayar_satu_gratis_satu']);
            $table->decimal('nilai_potongan', 10, 2)->default(0); // Nilai potongan (persen atau nominal)
            $table->string('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('santri_tertanggung_id')->nullable()->constrained('santris')->onDelete('set null'); // Untuk kasus 2 santri bayar 1
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keringanan_tagihans');
    }
};

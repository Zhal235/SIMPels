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
        Schema::create('tagihan_santris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->string('bulan')->comment('Format YYYY-MM untuk tagihan bulanan');
            $table->decimal('nominal_tagihan', 15, 2);
            $table->decimal('nominal_dibayar', 15, 2)->default(0);
            $table->decimal('nominal_keringanan', 10, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['santri_id', 'jenis_tagihan_id', 'tahun_ajaran_id', 'bulan'], 'idx_tagihan_santri_main');
            $table->index('status');
            $table->index(['santri_id', 'bulan']);
            $table->index('tanggal_jatuh_tempo');

            // Foreign keys
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');

            // Unique constraint untuk mencegah duplikasi tagihan
            $table->unique(['santri_id', 'jenis_tagihan_id', 'tahun_ajaran_id', 'bulan'], 'unique_tagihan_santri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_santris');
    }
};

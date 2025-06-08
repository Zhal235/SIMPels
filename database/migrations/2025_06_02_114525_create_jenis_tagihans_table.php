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
            
            // Added fields from add_class_santri_fields_to_jenis_tagihans_table
            $table->enum('tipe_pembayaran', ['manual', 'kelas'])->default('manual');
            $table->json('kelas_ids')->nullable();
            $table->enum('mode_santri', ['semua', 'individu'])->nullable();
            $table->json('santri_ids')->nullable();
            
            // Added field from add_buku_kas_id_to_jenis_tagihans_table
            $table->unsignedBigInteger('buku_kas_id')->nullable();
            
            // Added fields from add_default_jatuh_tempo_to_jenis_tagihans
            $table->integer('tanggal_jatuh_tempo')->nullable()->default(10)->comment('Tanggal jatuh tempo (1-31)');
            $table->integer('bulan_jatuh_tempo')->nullable()->default(0)->comment('Bulan jatuh tempo (0-11, 0=sama dengan bulan tagihan)');
            
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
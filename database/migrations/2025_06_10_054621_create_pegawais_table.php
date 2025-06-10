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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique()->nullable(); // Nomor Induk Pegawai
            $table->string('nama_pegawai');
            $table->string('nik')->unique(); // NIK KTP
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('no_hp')->nullable();
            $table->string('email')->unique()->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pendidikan_terakhir');
            $table->string('jurusan')->nullable();
            $table->string('institusi')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('divisi')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status_pegawai', ['Aktif', 'Non-Aktif', 'Pensiun', 'Resign']);
            $table->enum('jenis_pegawai', ['Tetap', 'Kontrak', 'Honorer', 'Magang']);
            $table->decimal('gaji_pokok', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};

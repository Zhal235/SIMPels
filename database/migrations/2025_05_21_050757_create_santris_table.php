<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSantrisTable extends Migration
{
    public function up()
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nis');

            // Data Pribadi
            $table->string('nisn')->nullable();
            $table->string('nik_santri')->nullable();
            $table->string('nama_santri');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('kelas')->nullable();
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->unsignedBigInteger('asrama_id')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('hobi')->nullable();
            $table->string('cita_cita')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->text('alamat');
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('no_kk')->nullable();

            // Data Ayah
            $table->string('nama_ayah');
            $table->string('nik_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('hp_ayah')->nullable();

            // Data Ibu
            $table->string('nama_ibu');
            $table->string('nik_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('hp_ibu')->nullable();

            // Data Bantuan & Kesejahteraan
            $table->string('no_bpjs')->nullable();
            $table->string('no_pkh')->nullable();
            $table->string('no_kip')->nullable();

            // Data Prestasi
            $table->string('bidang_prestasi')->nullable();
            $table->string('tingkat_prestasi')->nullable();
            $table->string('peringkat_prestasi')->nullable();
            $table->year('tahun_prestasi')->nullable();

            // Data Akademik Sebelumnya
            $table->string('npsn_sekolah')->nullable();
            $table->string('no_blanko_skhu')->nullable();
            $table->string('no_seri_ijazah')->nullable();
            $table->float('total_nilai_un', 5, 2)->nullable();
            $table->date('tanggal_kelulusan')->nullable();

            //foto
            $table->string('foto')->nullable();
            $table->string('status')->default('aktif');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('santris');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Langkah 1: Tambahkan kolom jenis_kas_id
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_kas_id')->nullable()->after('deskripsi');
        });

        // Langkah 2: Migrasi data dari kolom enum ke tabel baru
        // Pertama, kita harus memastikan bahwa tabel jenis_buku_kas sudah terisi data
        // Kemudian kita akan mengupdate kolom jenis_kas_id berdasarkan nilai jenis_kas

        // Langkah 3: Hapus kolom enum jenis_kas
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->dropColumn('jenis_kas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Langkah 1: Tambahkan kembali kolom enum jenis_kas
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->enum('jenis_kas', ['Operasional', 'Pembangunan', 'SPP', 'PSB', 'Insidental', 'Lainnya'])->default('Operasional')->after('deskripsi');
        });

        // Langkah 2: Migrasi data kembali dari relasi ke enum

        // Langkah 3: Hapus kolom jenis_kas_id
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->dropColumn('jenis_kas_id');
        });
    }
};

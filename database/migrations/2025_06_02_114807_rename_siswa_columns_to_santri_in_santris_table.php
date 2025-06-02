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
        Schema::table('santris', function (Blueprint $table) {
            $table->renameColumn('nik_siswa', 'nik_santri');
            $table->renameColumn('nama_siswa', 'nama_santri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->renameColumn('nik_santri', 'nik_siswa');
            $table->renameColumn('nama_santri', 'nama_siswa');
        });
    }
};

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
        // Check if old columns exist and new columns don't exist
        if (Schema::hasColumn('santris', 'nik_siswa') && !Schema::hasColumn('santris', 'nik_santri')) {
            Schema::table('santris', function (Blueprint $table) {
                $table->string('nik_santri')->nullable()->after('nik_siswa');
                $table->string('nama_santri')->after('nama_siswa');
            });
            
            // Copy data from old columns to new columns
            DB::statement('UPDATE santris SET nik_santri = nik_siswa, nama_santri = nama_siswa');
            
            Schema::table('santris', function (Blueprint $table) {
                $table->dropColumn(['nik_siswa', 'nama_siswa']);
            });
        }
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

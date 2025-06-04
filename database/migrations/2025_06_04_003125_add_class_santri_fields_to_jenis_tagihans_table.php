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
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->enum('tipe_pembayaran', ['manual', 'kelas'])->default('manual')->after('tahun_ajaran_id');
            $table->json('kelas_ids')->nullable()->after('tipe_pembayaran');
            $table->enum('mode_santri', ['semua', 'individu'])->nullable()->after('kelas_ids');
            $table->json('santri_ids')->nullable()->after('mode_santri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->dropColumn(['tipe_pembayaran', 'kelas_ids', 'mode_santri', 'santri_ids']);
        });
    }
};

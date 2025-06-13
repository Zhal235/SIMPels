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
        Schema::table('perizinan', function (Blueprint $table) {
            if (!Schema::hasColumn('perizinan', 'keperluan')) {
                $table->string('keperluan')->nullable()->after('jenis_izin');
            }
            
            if (!Schema::hasColumn('perizinan', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('alasan_ditolak');
            }
            
            if (!Schema::hasColumn('perizinan', 'lampiran')) {
                $table->string('lampiran')->nullable()->after('bukti');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perizinan', function (Blueprint $table) {
            $table->dropColumn(['keperluan', 'catatan_admin', 'lampiran']);
        });
    }
};

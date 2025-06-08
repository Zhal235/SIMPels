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
            $table->enum('target_type', ['all', 'kelas', 'santri'])->nullable()->after('bulan_jatuh_tempo')->comment('Target type for insidental billing');
            $table->json('target_kelas')->nullable()->after('target_type')->comment('Target kelas IDs for insidental billing');
            $table->json('target_santri')->nullable()->after('target_kelas')->comment('Target santri IDs for insidental billing');
            $table->boolean('is_nominal_per_kelas')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'target_kelas', 'target_santri']);
        });
    }
};

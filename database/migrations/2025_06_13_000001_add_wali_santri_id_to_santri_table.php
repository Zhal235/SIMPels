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
            if (!Schema::hasColumn('santris', 'wali_santri_id')) {
                $table->unsignedBigInteger('wali_santri_id')->nullable();
                $table->foreign('wali_santri_id')->references('id')->on('wali_santri')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            if (Schema::hasColumn('santris', 'wali_santri_id')) {
                $table->dropForeign(['wali_santri_id']);
                $table->dropColumn('wali_santri_id');
            }
        });
    }
};

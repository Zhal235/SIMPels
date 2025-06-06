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
        Schema::table('tagihan_santris', function (Blueprint $table) {
            $table->decimal('nominal_keringanan', 10, 2)->default(0)->after('nominal_dibayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_santris', function (Blueprint $table) {
            $table->dropColumn('nominal_keringanan');
        });
    }
};

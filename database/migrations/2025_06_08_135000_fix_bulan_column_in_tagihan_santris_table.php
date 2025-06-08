<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_santris', function (Blueprint $table) {
            $table->string('bulan', 3)->default('01')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_santris', function (Blueprint $table) {
            $table->string('bulan', 2)->nullable(false)->change();
        });
    }
};

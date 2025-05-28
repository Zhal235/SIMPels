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
        Schema::create('limit_tarik_tabungans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_setting');
            $table->decimal('limit_harian', 10, 2);
            $table->decimal('limit_bulanan', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limit_tarik_tabungans');
    }
};

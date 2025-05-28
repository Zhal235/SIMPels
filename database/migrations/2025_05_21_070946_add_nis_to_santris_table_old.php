<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('santris', function (Blueprint $table) {
        // hanya tambah kolom kalau belum ada
        if (! Schema::hasColumn('santris', 'nis')) {
            $table->string('nis')->after('id');
        }
    });
}


public function down(): void
{
    Schema::table('santris', function (Blueprint $table) {
        $table->dropColumn('nis');
    });
}

};

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
        $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('santris', function (Blueprint $table) {
        $table->dropForeign(['kelas_id']);
        $table->dropColumn('kelas_id');
    });
}

};

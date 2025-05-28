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
    Schema::table('rfid_tags', function (Blueprint $table) {
        $table->string('pin')->nullable()->after('tag_uid');
    });
}

public function down()
{
    Schema::table('rfid_tags', function (Blueprint $table) {
        $table->dropColumn('pin');
    });
}

};

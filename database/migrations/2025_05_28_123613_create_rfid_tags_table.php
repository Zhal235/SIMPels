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
    Schema::create('rfid_tags', function (Blueprint $table) {
        $table->id();
        $table->string('tag_uid')->unique();
        $table->foreignId('santri_id')
              ->nullable()
              ->constrained('santris')
              ->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
  public function down()
{
    Schema::dropIfExists('rfid_tags');
}

};

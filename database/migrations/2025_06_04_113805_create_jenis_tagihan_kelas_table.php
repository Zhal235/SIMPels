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
        Schema::create('jenis_tagihan_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_tagihan_id');
            $table->unsignedBigInteger('kelas_id');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();

            $table->foreign('jenis_tagihan_id')->references('id')->on('jenis_tagihans')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            
            // Ensure unique combination of jenis_tagihan and kelas
            $table->unique(['jenis_tagihan_id', 'kelas_id']);
        });

        // Add is_nominal_per_kelas to jenis_tagihans table
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->boolean('is_nominal_per_kelas')->default(false)->after('nominal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_tagihan_kelas');
        
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->dropColumn('is_nominal_per_kelas');
        });
    }
};

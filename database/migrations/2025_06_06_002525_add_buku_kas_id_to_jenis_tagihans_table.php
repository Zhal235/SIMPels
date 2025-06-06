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
            $table->foreignId('buku_kas_id')->nullable()->constrained('buku_kas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_tagihans', function (Blueprint $table) {
            $table->dropForeign(['buku_kas_id']);
            $table->dropColumn('buku_kas_id');
        });
    }
};

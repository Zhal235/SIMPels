<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            $table->string('status')->default('success')->after('nominal');
        });
    }
    public function down(): void {
        Schema::table('keuangan_transaksis', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

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
        Schema::create('hutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->nullable()->constrained('santris')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // For staff/other debts
            $table->string('keterangan');
            $table->decimal('jumlah_hutang', 10, 2);
            $table->date('tanggal_hutang');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('jumlah_terbayar', 10, 2)->default(0);
            $table->string('status')->default('belum lunas'); // 'belum lunas', 'lunas', 'sebagian'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutangs');
    }
};

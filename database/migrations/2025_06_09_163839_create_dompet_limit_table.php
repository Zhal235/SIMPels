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
        Schema::create('dompet_limit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dompet_id');
            $table->decimal('limit_harian', 15, 2)->default(0)->comment('Limit transaksi per hari');
            $table->decimal('limit_transaksi', 15, 2)->default(0)->comment('Limit per transaksi');
            $table->decimal('limit_mingguan', 15, 2)->nullable()->comment('Limit transaksi per minggu');
            $table->decimal('limit_bulanan', 15, 2)->nullable()->comment('Limit transaksi per bulan');
            $table->boolean('is_active')->default(true)->comment('Status aktif limit');
            $table->text('catatan')->nullable()->comment('Catatan atau alasan limit');
            $table->timestamps();
            
            // Foreign key dan index
            $table->foreign('dompet_id')->references('id')->on('dompet')->onDelete('cascade');
            $table->index('dompet_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dompet_limit');
    }
};

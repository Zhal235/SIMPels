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
        Schema::create('dompet', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_pemilik', ['santri', 'asatidz']);
            $table->unsignedBigInteger('pemilik_id'); // ID santri atau user (asatidz)
            $table->string('nomor_dompet')->unique();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->decimal('limit_transaksi', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['jenis_pemilik', 'pemilik_id']);
            $table->index('nomor_dompet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dompet');
    }
};

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
        // Only create if the table doesn't already exist
        if (!Schema::hasTable('bidangs')) {
            Schema::create('bidangs', function (Blueprint $table) {
                $table->id();
                $table->string('nama_bidang', 100);
                $table->string('kode_bidang', 20)->unique();
                $table->text('deskripsi')->nullable();
                $table->foreignId('naib_penanggung_jawab_id')
                    ->nullable()
                    ->constrained('pegawais')
                    ->nullOnDelete();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->integer('urutan')->default(1); // Urutan untuk tampilan
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidangs');
    }
};

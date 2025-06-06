<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JenisBukuKas;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed jenis buku kas terlebih dahulu
        $seeder = new \Database\Seeders\JenisBukuKasSeeder();
        $seeder->run();

        // Dapatkan mapping data
        $jenisKasMapping = JenisBukuKas::pluck('id', 'nama')->toArray();

        // Update data di buku_kas, mengisi jenis_kas_id berdasarkan nilai jenis_kas
        $bukuKas = DB::table('buku_kas')->get();
        
        foreach ($bukuKas as $kas) {
            $jenisKas = $kas->jenis_kas ?? 'Operasional';
            $jenisKasId = $jenisKasMapping[$jenisKas] ?? $jenisKasMapping['Operasional'];
            
            DB::table('buku_kas')
                ->where('id', $kas->id)
                ->update(['jenis_kas_id' => $jenisKasId]);
        }

        // Tambahkan foreign key constraint setelah semua data dimigrasi
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->foreign('jenis_kas_id')
                  ->references('id')
                  ->on('jenis_buku_kas')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus foreign key constraint
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->dropForeign(['jenis_kas_id']);
        });

        // Kembalikan data dari jenis_kas_id ke jenis_kas (enum)
        $jenisKasMapping = array_flip(JenisBukuKas::pluck('nama', 'id')->toArray());
        
        $bukuKas = DB::table('buku_kas')->get();
        
        foreach ($bukuKas as $kas) {
            $jenisKasId = $kas->jenis_kas_id;
            $jenisKas = $jenisKasMapping[$jenisKasId] ?? 'Operasional';
            
            DB::table('buku_kas')
                ->where('id', $kas->id)
                ->update(['jenis_kas' => $jenisKas]);
        }
    }
};

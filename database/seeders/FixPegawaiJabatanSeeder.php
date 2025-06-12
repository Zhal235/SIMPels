<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\PegawaiJabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixPegawaiJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds to fix pegawai_jabatan records.
     * This will ensure each pegawai has a proper jabatan record.
     */
    public function run(): void
    {
        // Clear existing data from pivot table
        DB::table('pegawai_jabatan')->truncate();
        
        // Get all pegawai with jabatan_id
        $pegawais = Pegawai::whereNotNull('jabatan_id')->get();
        
        $this->command->info('Found ' . $pegawais->count() . ' pegawai with jabatan_id');
        
        foreach ($pegawais as $pegawai) {
            $jabatan = Jabatan::find($pegawai->jabatan_id);
            
            if ($jabatan) {
                // Create entry in pegawai_jabatan table
                PegawaiJabatan::create([
                    'pegawai_id' => $pegawai->id,
                    'jabatan_id' => $jabatan->id,
                    'is_jabatan_utama' => true,
                    'tanggal_mulai' => $pegawai->tanggal_masuk ?: now(),
                    'status' => 'aktif'
                ]);
                
                $this->command->info("Added jabatan {$jabatan->nama_jabatan} for {$pegawai->nama_pegawai}");
            } else {
                $this->command->warn("Jabatan not found for pegawai {$pegawai->nama_pegawai} (ID: {$pegawai->jabatan_id})");
            }
        }
        
        $this->command->info('Completed adding jabatan records to pegawai_jabatan table');
    }
}

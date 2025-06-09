<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Santri;
use App\Models\DompetSantri;

class CreateAllSantriDompetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua santri yang belum memiliki dompet
        $santriTanpaDompet = Santri::whereDoesntHave('dompetSantri')->get();
        
        foreach ($santriTanpaDompet as $santri) {
            // Buat nomor dompet unik
            $nomorDompet = 'DS' . date('Y') . str_pad($santri->id, 4, '0', STR_PAD_LEFT);
            
            // Pastikan nomor dompet belum ada
            while (DompetSantri::where('nomor_dompet', $nomorDompet)->exists()) {
                $nomorDompet = 'DS' . date('Y') . str_pad($santri->id, 4, '0', STR_PAD_LEFT) . rand(10, 99);
            }
            
            DompetSantri::create([
                'santri_id' => $santri->id,
                'nomor_dompet' => $nomorDompet,
                'nama_pemilik' => $santri->nama_santri,
                'saldo' => 0,
                'limit_transaksi' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Berhasil membuat dompet untuk ' . $santriTanpaDompet->count() . ' santri.');
    }
}

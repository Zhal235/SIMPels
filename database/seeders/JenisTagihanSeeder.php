<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisTagihan;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class JenisTagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active tahun ajaran
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        // Clear existing data
        \DB::table('jenis_tagihan_kelas')->delete();
        \DB::table('jenis_tagihans')->delete();

        // Create SPP with different amounts per class
        $spp = JenisTagihan::create([
            'nama' => 'SPP',
            'nominal' => 300000, // Default amount
            'is_nominal_per_kelas' => true,
            'is_bulanan' => true,
            'bulan_pembayaran' => ["07","08","09","10","11","12","01","02","03","04","05","06"],
            'deskripsi' => 'Sumbangan Pembinaan Pendidikan',
            'tahun_ajaran_id' => $tahunAjaran?->id,
            'kategori_tagihan' => 'Rutin'
        ]);

        // Different amounts per class level
        $amounts = [
            'VII' => 300000,
            'VIII' => 350000,
            'IX' => 400000
        ];

        // Set amounts per class
        $kelas = Kelas::all();
        foreach ($kelas as $kls) {
            $nominal = $amounts[$kls->tingkat] ?? $spp->nominal;
            $spp->setNominalForKelas($kls->id, $nominal);
        }

        // Create Uang Gedung (one-time payment)
        JenisTagihan::create([
            'nama' => 'Uang Gedung',
            'nominal' => 2500000,
            'is_nominal_per_kelas' => false,
            'is_bulanan' => false,
            'deskripsi' => 'Pembayaran satu kali saat masuk',
            'tahun_ajaran_id' => $tahunAjaran?->id,
            'kategori_tagihan' => 'Insidental'
        ]);

        // Create Seragam with different amounts per class
        $seragam = JenisTagihan::create([
            'nama' => 'Seragam',
            'nominal' => 500000,
            'is_nominal_per_kelas' => true,
            'is_bulanan' => false,
            'deskripsi' => 'Biaya seragam sekolah',
            'tahun_ajaran_id' => $tahunAjaran?->id,
            'kategori_tagihan' => 'Insidental'
        ]);

        // Different seragam amounts per class level
        $seragamAmounts = [
            'VII' => 600000, // New students need complete set
            'VIII' => 300000, // Might need replacements
            'IX' => 300000 // Might need replacements
        ];

        // Set seragam amounts per class
        foreach ($kelas as $kls) {
            $nominal = $seragamAmounts[$kls->tingkat] ?? $seragam->nominal;
            $seragam->setNominalForKelas($kls->id, $nominal);
        }
    }
}

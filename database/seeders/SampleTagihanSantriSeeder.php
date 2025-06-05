<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;

class SampleTagihanSantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeTahunAjaran = TahunAjaran::where('is_active', 1)->first();
        
        if (!$activeTahunAjaran) {
            echo "Tidak ada tahun ajaran aktif\n";
            return;
        }

        // Ambil 10 santri pertama untuk testing
        $santris = Santri::where('status', 'aktif')->take(10)->get();
        $jenisTagihans = JenisTagihan::all();

        echo "Membuat tagihan untuk " . $santris->count() . " santri\n";
        echo "Jenis tagihan yang tersedia: " . $jenisTagihans->count() . "\n";

        $count = 0;
        
        foreach ($santris as $santri) {
            echo "Memproses santri: {$santri->nama_santri} (ID: {$santri->id})\n";
            
            foreach ($jenisTagihans as $jenis) {
                echo "  - Jenis tagihan: {$jenis->nama} (Kategori: {$jenis->kategori_tagihan}, Bulanan: " . ($jenis->is_bulanan ? 'Ya' : 'Tidak') . ")\n";
                
                if ($jenis->kategori_tagihan === 'Rutin' && $jenis->is_bulanan) {
                    // Untuk tagihan rutin bulanan, buat tagihan untuk beberapa bulan
                    $bulanList = ['2025-01', '2025-02', '2025-03', '2025-04'];
                    
                    foreach ($bulanList as $bulan) {
                        $tagihan = TagihanSantri::create([
                            'santri_id' => $santri->id,
                            'jenis_tagihan_id' => $jenis->id,
                            'tahun_ajaran_id' => $activeTahunAjaran->id,
                            'bulan' => $bulan,
                            'nominal_tagihan' => $jenis->nominal,
                            'nominal_dibayar' => 0, // Semua belum bayar
                            'status' => 'aktif'
                        ]);
                        $count++;
                        echo "    * Tagihan rutin bulan {$bulan}: Rp " . number_format($jenis->nominal) . "\n";
                    }
                } else {
                    // Untuk tagihan insidentil atau rutin tahunan
                    $tagihan = TagihanSantri::create([
                        'santri_id' => $santri->id,
                        'jenis_tagihan_id' => $jenis->id,
                        'tahun_ajaran_id' => $activeTahunAjaran->id,
                        'bulan' => $activeTahunAjaran->tahun_mulai, // gunakan tahun untuk insidentil
                        'nominal_tagihan' => $jenis->nominal,
                        'nominal_dibayar' => 0, // Semua belum bayar
                        'status' => 'aktif'
                    ]);
                    $count++;
                    echo "    * Tagihan insidentil: Rp " . number_format($jenis->nominal) . "\n";
                }
            }
            echo "\n";
        }

        echo "SELESAI: Created {$count} TagihanSantri records\n";
        echo "Semua tagihan dibuat dengan status BELUM BAYAR (nominal_dibayar = 0)\n";
    }
}

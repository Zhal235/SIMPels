<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\TagihanSantri;
use App\Models\JenisTagihanKelas;

echo "=== Debug Generate Tagihan ===\n";

// Ambil tahun ajaran aktif
$activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
echo "Tahun Ajaran: {$activeTahunAjaran->nama_tahun_ajaran}\n";

// Ambil 1 santri untuk test
$santri = Santri::where('status', 'aktif')->with('kelasRelasi')->first();
echo "Santri: {$santri->nama_santri} (NIS: {$santri->nis})\n";
echo "Kelas: " . $santri->kelasRelasi->pluck('nama')->implode(', ') . "\n";

// Ambil jenis tagihan
$jenisTagihans = JenisTagihan::all();
echo "Jenis Tagihan: " . $jenisTagihans->count() . "\n";

foreach ($jenisTagihans as $jenisTagihan) {
    echo "\n--- Processing: {$jenisTagihan->nama} ---\n";
    echo "Kategori: {$jenisTagihan->kategori_tagihan}\n";
    echo "Is Bulanan: " . ($jenisTagihan->is_bulanan ? 'true' : 'false') . "\n";
    echo "Nominal Default: {$jenisTagihan->nominal}\n";
    
    // Tentukan nominal berdasarkan kelas jika ada
    $nominal = $jenisTagihan->nominal;
    $kelasNames = $santri->kelasRelasi->pluck('nama')->toArray();
    
    if ($jenisTagihan->is_nominal_per_kelas && !empty($kelasNames)) {
        foreach ($kelasNames as $kelasName) {
            $kelas = \App\Models\Kelas::where('nama', $kelasName)->first();
            if ($kelas) {
                $jenisTagihanKelas = JenisTagihanKelas::where('jenis_tagihan_id', $jenisTagihan->id)
                    ->where('kelas_id', $kelas->id)
                    ->first();
                
                if ($jenisTagihanKelas) {
                    $nominal = $jenisTagihanKelas->nominal;
                    echo "Nominal per kelas {$kelasName}: {$nominal}\n";
                    break;
                }
            }
        }
    }
    
    if ($jenisTagihan->kategori_tagihan === 'Rutin' && $jenisTagihan->is_bulanan) {
        echo "Generating RUTIN BULANAN...\n";
        
        // Generate bulan list
        $bulanList = [];
        $tahunMulai = (int) $activeTahunAjaran->tahun_mulai;
        $tahunAkhir = (int) $activeTahunAjaran->tahun_selesai;
        
        echo "Tahun Mulai: {$tahunMulai}, Tahun Akhir: {$tahunAkhir}\n";
        
        // Juli - Desember (tahun mulai)
        for ($i = 7; $i <= 12; $i++) {
            $bulanList[] = $tahunMulai . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        // Januari - Juni (tahun akhir)
        for ($i = 1; $i <= 6; $i++) {
            $bulanList[] = $tahunAkhir . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        echo "Bulan List: " . implode(', ', $bulanList) . "\n";
        
        foreach ($bulanList as $bulan) {
            echo "Creating tagihan for bulan: {$bulan}\n";
            
            TagihanSantri::create([
                'santri_id' => $santri->id,
                'jenis_tagihan_id' => $jenisTagihan->id,
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'bulan' => $bulan,
                'nominal_tagihan' => $nominal,
                'nominal_dibayar' => 0,
                'status' => 'aktif'
            ]);
        }
    } else {
        echo "Generating INSIDENTIL/TAHUNAN...\n";
        echo "Bulan will be: {$activeTahunAjaran->tahun_mulai}\n";
        
        TagihanSantri::create([
            'santri_id' => $santri->id,
            'jenis_tagihan_id' => $jenisTagihan->id,
            'tahun_ajaran_id' => $activeTahunAjaran->id,
            'bulan' => $activeTahunAjaran->tahun_mulai, // Ini yang bermasalah!
            'nominal_tagihan' => $nominal,
            'nominal_dibayar' => 0,
            'status' => 'aktif'
        ]);
    }
}

echo "\n=== Hasil Generate ===\n";
$tagihan = TagihanSantri::where('santri_id', $santri->id)->with('jenisTagihan')->get();
foreach ($tagihan as $t) {
    echo "Jenis: {$t->jenisTagihan->nama}, Bulan: '{$t->bulan}', Nominal: {$t->nominal_tagihan}\n";
}

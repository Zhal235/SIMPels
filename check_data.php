<?php

require_once 'vendor/autoload.php';

use App\Models\JenisTagihan;
use App\Models\TahunAjaran;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== DATA JENIS TAGIHAN ===\n";
    $jenisTagihans = JenisTagihan::all();
    
    if ($jenisTagihans->isEmpty()) {
        echo "❌ Tidak ada jenis tagihan ditemukan. Membuat default...\n";
        $spp = JenisTagihan::create([
            'nama' => 'SPP',
            'keterangan' => 'Sumbangan Pembinaan Pendidikan',
            'nominal' => 250000,
            'is_active' => true
        ]);
        echo "✓ Jenis tagihan SPP dibuat dengan ID: {$spp->id}\n";
    } else {
        foreach ($jenisTagihans as $jt) {
            echo "ID: {$jt->id}, Nama: {$jt->nama}, Nominal: Rp " . number_format($jt->nominal) . "\n";
        }
    }

    echo "\n=== DATA TAHUN AJARAN ===\n";
    $tahunAjarans = TahunAjaran::all();
    
    if ($tahunAjarans->isEmpty()) {
        echo "❌ Tidak ada tahun ajaran ditemukan. Membuat default...\n";
        $tahunAjaran = TahunAjaran::create([
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'semester' => 2,
            'is_active' => true
        ]);
        echo "✓ Tahun ajaran 2024-2025 dibuat dengan ID: {$tahunAjaran->id}\n";
    } else {
        foreach ($tahunAjarans as $ta) {
            echo "ID: {$ta->id}, Tahun: {$ta->tahun_mulai}-{$ta->tahun_selesai}, Semester: {$ta->semester}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

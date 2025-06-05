<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TahunAjaran;

echo "=== Detail Tahun Ajaran Aktif ===\n";
$tahunAjaran = TahunAjaran::where('is_active', true)->first();
if ($tahunAjaran) {
    echo "ID: {$tahunAjaran->id}\n";
    echo "Nama: {$tahunAjaran->nama_tahun_ajaran}\n";
    echo "Tahun Mulai: '{$tahunAjaran->tahun_mulai}' (type: " . gettype($tahunAjaran->tahun_mulai) . ")\n";
    echo "Tahun Selesai: '{$tahunAjaran->tahun_selesai}' (type: " . gettype($tahunAjaran->tahun_selesai) . ")\n";
    echo "Is Active: " . ($tahunAjaran->is_active ? 'true' : 'false') . "\n";
    
    echo "\n=== Casting to Int ===\n";
    $tahunMulai = (int) $tahunAjaran->tahun_mulai;
    $tahunSelesai = (int) $tahunAjaran->tahun_selesai;
    echo "Int Tahun Mulai: {$tahunMulai}\n";
    echo "Int Tahun Selesai: {$tahunSelesai}\n";
    
    echo "\n=== Test Generate Bulan List ===\n";
    $bulanList = [];
    
    // Juli - Desember (tahun mulai)
    for ($i = 7; $i <= 12; $i++) {
        $bulan = $tahunMulai . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $bulanList[] = $bulan;
        echo "Generated: {$bulan}\n";
    }
    
    // Januari - Juni (tahun selesai)
    for ($i = 1; $i <= 6; $i++) {
        $bulan = $tahunSelesai . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $bulanList[] = $bulan;
        echo "Generated: {$bulan}\n";
    }
} else {
    echo "Tidak ada tahun ajaran aktif!\n";
}

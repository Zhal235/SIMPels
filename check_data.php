<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

echo "=== CHECKING JENIS TAGIHAN ===\n";

try {
    $count = App\Models\JenisTagihan::count();
    echo "Total Jenis Tagihan: $count\n\n";
    
    if ($count > 0) {
        $jenisTagihans = App\Models\JenisTagihan::take(5)->get();
        echo "Sample Jenis Tagihan:\n";
        foreach ($jenisTagihans as $jenis) {
            echo "ID: {$jenis->id} - Nama: {$jenis->nama} - Nominal: Rp " . number_format($jenis->nominal) . "\n";
        }
    } else {
        echo "No Jenis Tagihan found!\n";
    }
    
    echo "\n=== CHECKING TAHUN AJARAN ===\n";
    $activeYear = App\Models\TahunAjaran::where('status', 'aktif')->first();
    if ($activeYear) {
        echo "Active Year: {$activeYear->nama} (ID: {$activeYear->id})\n";
    } else {
        echo "No active year found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

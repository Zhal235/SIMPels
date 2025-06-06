<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Jenis Tagihan Data ===\n";

// Check total records
$total = App\Models\JenisTagihan::count();
echo "Total jenis tagihan: $total\n\n";

if ($total > 0) {
    echo "Sample data (first 5 records):\n";
    $jenisTagihan = App\Models\JenisTagihan::limit(5)->get(['id', 'nama', 'buku_kas_id', 'created_at']);
    
    foreach ($jenisTagihan as $item) {
        echo "ID: {$item->id}, Nama: {$item->nama}, Buku Kas ID: {$item->buku_kas_id}\n";
    }
} else {
    echo "No jenis tagihan found!\n";
}

echo "\n=== Checking Buku Kas Data ===\n";
$totalBukuKas = App\Models\BukuKas::count();
echo "Total buku kas: $totalBukuKas\n";

if ($totalBukuKas > 0) {
    echo "Sample buku kas data:\n";
    $bukuKas = App\Models\BukuKas::limit(5)->get(['id', 'nama', 'jenis']);
    
    foreach ($bukuKas as $item) {
        echo "ID: {$item->id}, Nama: {$item->nama}, Jenis: {$item->jenis}\n";
    }
}

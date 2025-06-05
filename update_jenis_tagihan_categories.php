<?php

require_once 'vendor/autoload.php';

use App\Models\JenisTagihan;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATING JENIS TAGIHAN CATEGORIES ===\n\n";

echo "Current Jenis Tagihan:\n";
$jenisTagihans = JenisTagihan::all();
foreach ($jenisTagihans as $jenis) {
    echo "- {$jenis->nama}: {$jenis->kategori_tagihan}\n";
}

echo "\nUpdating Uang Gedung and Seragam to Insidentil...\n";

// Update Uang Gedung and Seragam to be Insidentil
JenisTagihan::whereIn('nama', ['Uang Gedung', 'Seragam'])
    ->update(['kategori_tagihan' => 'Insidentil']);

echo "Updated!\n";

echo "\nUpdated Jenis Tagihan:\n";
$jenisTagihans = JenisTagihan::all();
foreach ($jenisTagihans as $jenis) {
    echo "- {$jenis->nama}: {$jenis->kategori_tagihan}\n";
}

echo "\n=== COMPLETE ===\n";

?>

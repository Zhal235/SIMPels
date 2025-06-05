<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TagihanSantri;
use App\Models\JenisTagihan;

echo "=== Sample Tagihan Santri ===\n";
$tagihan = TagihanSantri::with(['jenisTagihan', 'santri'])
    ->take(10)->get();
    
foreach($tagihan as $t) {
    echo "ID: {$t->id}, Santri: {$t->santri->nama}, Jenis: {$t->jenisTagihan->nama}, Bulan: {$t->bulan_tagihan}, Nominal: {$t->nominal}\n";
}

echo "\n=== Count by Bulan ===\n";
$countByBulan = TagihanSantri::selectRaw('bulan, COUNT(*) as count')
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();
    
foreach($countByBulan as $c) {
    echo "Bulan: {$c->bulan}, Count: {$c->count}\n";
}

echo "\n=== Count by Jenis Tagihan ===\n";
$countByJenis = TagihanSantri::with('jenisTagihan')
    ->selectRaw('jenis_tagihan_id, COUNT(*) as count')
    ->groupBy('jenis_tagihan_id')
    ->get();
    
foreach($countByJenis as $c) {
    echo "Jenis: {$c->jenisTagihan->nama} ({$c->jenisTagihan->tipe}), Count: {$c->count}\n";
}

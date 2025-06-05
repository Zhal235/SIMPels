<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== EXACT KATEGORI VALUES ===\n";
$jenis = DB::table('jenis_tagihans')->select('kategori_tagihan')->distinct()->get();
foreach ($jenis as $j) {
    echo "Value: [{$j->kategori_tagihan}]\n";
    echo "Length: " . strlen($j->kategori_tagihan) . "\n";
    echo "Hex: " . bin2hex($j->kategori_tagihan) . "\n\n";
}

echo "=== ALL JENIS TAGIHAN ===\n";
$all_jenis = DB::table('jenis_tagihans')->get();
foreach ($all_jenis as $jenis) {
    echo "ID: {$jenis->id} | Nama: {$jenis->nama} | Kategori: [{$jenis->kategori_tagihan}]\n";
}

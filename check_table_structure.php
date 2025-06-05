<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Struktur Tabel tagihan_santris ===\n";
$columns = DB::select('DESCRIBE tagihan_santris');
foreach($columns as $col) {
    echo $col->Field . ' (' . $col->Type . ') - ' . ($col->Null == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

echo "\n=== Sample Data TagihanSantri ===\n";
$sample = DB::select('SELECT * FROM tagihan_santris LIMIT 5');
foreach($sample as $s) {
    echo json_encode($s) . "\n";
}

<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Jumlah buku kas: " . \App\Models\BukuKas::count() . PHP_EOL;

$bukuKasList = \App\Models\BukuKas::take(3)->get();
foreach ($bukuKasList as $kas) {
    echo "ID: {$kas->id}, Nama: {$kas->nama_kas}" . PHP_EOL;
}

echo PHP_EOL . "Testing API endpoint manually..." . PHP_EOL;

try {
    $bukuKas = \App\Models\BukuKas::findOrFail(1);
    echo "Buku kas ID 1 ditemukan: " . $bukuKas->nama_kas . PHP_EOL;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

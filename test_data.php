<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->boot();

echo "=== TESTING DATA ===\n";
echo "Jumlah Jenis Tagihan: " . App\Models\JenisTagihan::count() . "\n";
echo "Jumlah Santri: " . App\Models\Santri::count() . "\n";
echo "Jumlah Transaksi: " . App\Models\Transaksi::count() . "\n";

echo "\n=== TESTING TRANSAKSI MODEL ===\n";
$transaksi = new App\Models\Transaksi();
echo "Table name: " . $transaksi->getTable() . "\n";

echo "\n=== TESTING ACTIVE YEAR SCOPE ===\n";
try {
    $activeYear = App\Models\TahunAjaran::where('status', 'aktif')->first();
    if ($activeYear) {
        echo "Active year: " . $activeYear->nama . "\n";
        $result = App\Models\Transaksi::activeYear()->count();
        echo "Transaksi with active year: " . $result . "\n";
    } else {
        echo "No active year found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

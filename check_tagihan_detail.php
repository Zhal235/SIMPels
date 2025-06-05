<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\Santri;

// Initialize database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'simpels_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Detailed Tagihan Check ===\n";

// Get sample tagihan dengan relasi
$tagihanSample = TagihanSantri::with(['santri', 'jenisTagihan'])
    ->limit(5)
    ->get();

foreach($tagihanSample as $tagihan) {
    echo "ID: {$tagihan->id}\n";
    echo "  Santri ID: {$tagihan->santri_id}\n";
    echo "  Santri Name: " . ($tagihan->santri ? $tagihan->santri->nama : 'NULL') . "\n";
    echo "  Jenis ID: {$tagihan->jenis_tagihan_id}\n";
    echo "  Jenis Name: " . ($tagihan->jenisTagihan ? $tagihan->jenisTagihan->nama : 'NULL') . "\n";
    echo "  Bulan: {$tagihan->bulan}\n";
    echo "  Nominal Tagihan: {$tagihan->nominal_tagihan}\n";
    echo "  Nominal Dibayar: {$tagihan->nominal_dibayar}\n";
    echo "  Status: {$tagihan->status}\n";
    echo "\n";
}

echo "\n=== Check for NULL or Empty Values ===\n";
$nullChecks = [
    'santri_id IS NULL' => TagihanSantri::whereNull('santri_id')->count(),
    'jenis_tagihan_id IS NULL' => TagihanSantri::whereNull('jenis_tagihan_id')->count(),
    'bulan IS NULL or empty' => TagihanSantri::where(function($q) {
        $q->whereNull('bulan')->orWhere('bulan', '');
    })->count(),
    'nominal_tagihan IS NULL or 0' => TagihanSantri::where(function($q) {
        $q->whereNull('nominal_tagihan')->orWhere('nominal_tagihan', 0);
    })->count(),
];

foreach($nullChecks as $check => $count) {
    echo "{$check}: {$count}\n";
}

echo "\n=== Monthly Distribution ===\n";
$monthlyDist = TagihanSantri::selectRaw('bulan, COUNT(*) as count, SUM(nominal_tagihan) as total_nominal')
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();

foreach($monthlyDist as $month) {
    echo "Bulan: {$month->bulan}, Count: {$month->count}, Total: Rp " . number_format($month->total_nominal) . "\n";
}

?>

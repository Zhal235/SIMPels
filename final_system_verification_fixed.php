<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== FINAL SYSTEM VERIFICATION ===\n\n";

// 1. Check if database has correct data format
echo "1. DATABASE STRUCTURE VERIFICATION:\n";
echo "-----------------------------------\n";

// Check bulan format
$bulan_check = DB::table('tagihan_santris')
    ->select('bulan')
    ->whereNotNull('bulan')
    ->distinct()
    ->orderBy('bulan')
    ->limit(10)
    ->get();

echo "Sample bulan formats:\n";
foreach ($bulan_check as $row) {
    echo "  - " . $row->bulan . "\n";
}

// Check kategori tagihan
$kategori_stats = DB::table('tagihan_santris as ts')
    ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
    ->select(
        DB::raw('jt.kategori_tagihan'),
        DB::raw('COUNT(*) as count')
    )
    ->groupBy('jt.kategori_tagihan')
    ->get();

echo "\nTagihan distribution by category:\n";
foreach ($kategori_stats as $stat) {
    $category = $stat->kategori_tagihan ?: 'NULL';
    echo "  - $category: {$stat->count} records\n";
}

// 2. Test API endpoint
echo "\n\n2. API ENDPOINT TEST:\n";
echo "---------------------\n";

// Get first santri for testing
$first_santri = DB::table('santris')->first();
if ($first_santri) {
    echo "Testing with santri: {$first_santri->nama_santri} (ID: {$first_santri->id})\n";
    
    // Simulate API call for Rutin
    $tagihan_rutin = DB::table('tagihan_santris as ts')
        ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->where('ts.santri_id', $first_santri->id)
        ->where('ts.status', 'aktif')
        ->where('jt.kategori_tagihan', 'Rutin')
        ->select('ts.*', 'jt.nama', 'jt.kategori_tagihan')
        ->get();
    
    // Simulate API call for Insidental
    $tagihan_insidentil = DB::table('tagihan_santris as ts')
        ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->where('ts.santri_id', $first_santri->id)
        ->where('ts.status', 'aktif')
        ->where('jt.kategori_tagihan', 'Insidental')
        ->select('ts.*', 'jt.nama', 'jt.kategori_tagihan')
        ->get();
    
    echo "Tagihan Rutin found: " . count($tagihan_rutin) . "\n";
    echo "Tagihan Insidental found: " . count($tagihan_insidentil) . "\n";
    
    if (count($tagihan_rutin) > 0) {
        echo "\nSample Rutin tagihan:\n";
        foreach (array_slice($tagihan_rutin->toArray(), 0, 3) as $tagihan) {
            echo "  - {$tagihan->nama} | {$tagihan->bulan} | Rp " . number_format($tagihan->nominal_tagihan) . "\n";
        }
    }
    
    if (count($tagihan_insidentil) > 0) {
        echo "\nSample Insidental tagihan:\n";
        foreach (array_slice($tagihan_insidentil->toArray(), 0, 3) as $tagihan) {
            echo "  - {$tagihan->nama} | {$tagihan->bulan} | Rp " . number_format($tagihan->nominal_tagihan) . "\n";
        }
    }
}

// 3. Check month grouping logic
echo "\n\n3. MONTH GROUPING VERIFICATION:\n";
echo "-------------------------------\n";

$months_data = DB::table('tagihan_santris as ts')
    ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
    ->select('ts.bulan', 'jt.kategori_tagihan', DB::raw('COUNT(*) as count'))
    ->whereNotNull('ts.bulan')
    ->groupBy('ts.bulan', 'jt.kategori_tagihan')
    ->orderBy('ts.bulan')
    ->get();

$month_summary = [];
foreach ($months_data as $row) {
    $month = $row->bulan;
    $category = $row->kategori_tagihan ?: 'Unknown';
    
    if (!isset($month_summary[$month])) {
        $month_summary[$month] = ['Rutin' => 0, 'Insidental' => 0, 'Unknown' => 0];
    }
    $month_summary[$month][$category] = $row->count;
}

echo "Month distribution:\n";
foreach (array_slice($month_summary, 0, 5, true) as $month => $data) {
    echo "  $month: Rutin={$data['Rutin']}, Insidental={$data['Insidental']}, Unknown={$data['Unknown']}\n";
}

// 4. Frontend data format test
echo "\n\n4. FRONTEND DATA FORMAT TEST:\n";
echo "-----------------------------\n";

// Test the format that will be sent to frontend
if ($first_santri) {
    $sample_tagihan = DB::table('tagihan_santris as ts')
        ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->where('ts.santri_id', $first_santri->id)
        ->where('ts.status', 'aktif')
        ->select('ts.*', 'jt.nama', 'jt.kategori_tagihan', 'jt.is_bulanan as jt_is_bulanan')
        ->first();
    
    if ($sample_tagihan) {
        echo "Sample tagihan data structure:\n";
        echo "  - ID: {$sample_tagihan->id}\n";
        echo "  - Nama Tagihan: {$sample_tagihan->nama}\n";
        echo "  - Kategori: " . ($sample_tagihan->kategori_tagihan ?: 'NULL') . "\n";
        echo "  - Is Bulanan (jenis): " . ($sample_tagihan->jt_is_bulanan ? 'true' : 'false') . "\n";
        echo "  - Bulan: {$sample_tagihan->bulan}\n";
        echo "  - Jumlah: Rp " . number_format($sample_tagihan->nominal_tagihan) . "\n";
        echo "  - Status: {$sample_tagihan->status}\n";
        
        // Test categorization logic
        $is_rutin = ($sample_tagihan->kategori_tagihan == 'Rutin');
        $is_insidentil = ($sample_tagihan->kategori_tagihan == 'Insidental');
        
        echo "  - Categorized as Rutin: " . ($is_rutin ? 'YES' : 'NO') . "\n";
        echo "  - Categorized as Insidental: " . ($is_insidentil ? 'YES' : 'NO') . "\n";
    }
}

echo "\n\n=== VERIFICATION COMPLETE ===\n";
echo "System is ready for testing!\n";
echo "Please check the browser at: http://127.0.0.1:8000/login\n";
echo "Navigate to: Keuangan -> Pembayaran Santri\n";
echo "Expected features:\n";
echo "  - Two tabs: 'Rutin' and 'Insidentil'\n";
echo "  - Proper filtering by category\n";
echo "  - Month format: YYYY-MM displayed as user-friendly format\n";
echo "  - Selection reset when switching tabs\n";
echo "  - All existing payment functionality preserved\n";

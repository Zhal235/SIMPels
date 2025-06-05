<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE STATUS CHECK ===\n\n";

try {
    // Check status values
    $status_values = DB::table('tagihan_santris')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "Status values in database:\n";
    foreach ($status_values as $row) {
        echo "  - '{$row->status}': {$row->count} records\n";
    }
    
    // Check if there are fields we expect but missing
    echo "\nSample record structure:\n";
    $sample = DB::table('tagihan_santris as ts')
        ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->select('ts.*', 'jt.nama_tagihan', 'jt.kategori_tagihan')
        ->first();
    
    if ($sample) {
        echo "Available fields:\n";
        foreach ((array)$sample as $field => $value) {
            $display_value = is_null($value) ? 'NULL' : (is_string($value) ? "'$value'" : $value);
            echo "  - $field: $display_value\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

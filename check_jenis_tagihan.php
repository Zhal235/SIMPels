<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== JENIS TAGIHAN TABLE CHECK ===\n\n";

try {
    // Check jenis_tagihans structure
    $columns = DB::select('DESCRIBE jenis_tagihans');
    echo "jenis_tagihans table structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    // Sample data
    $sample = DB::table('jenis_tagihans')->limit(3)->get();
    echo "\nSample jenis_tagihans data:\n";
    foreach ($sample as $row) {
        echo "  - ID: {$row->id}\n";
        foreach ((array)$row as $field => $value) {
            if ($field != 'id') {
                $display_value = is_null($value) ? 'NULL' : (is_string($value) ? "'$value'" : $value);
                echo "    $field: $display_value\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE TABLE CHECK ===\n\n";

try {
    // Check what tables exist
    $tables = DB::select('SHOW TABLES');
    echo "Available tables:\n";
    foreach ($tables as $table) {
        $table_name = array_values((array)$table)[0];
        echo "  - $table_name\n";
    }
    
    echo "\n";
    
    // Check if tagihan_santri exists
    $tagihan_exists = false;
    foreach ($tables as $table) {
        $table_name = array_values((array)$table)[0];
        if ($table_name == 'tagihan_santris') {
            $tagihan_exists = true;
            break;
        }
    }
    
    if ($tagihan_exists) {
        echo "tagihan_santris table EXISTS\n";
        
        // Check structure
        $columns = DB::select('DESCRIBE tagihan_santris');
        echo "\nTable structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
        
        // Check data count
        $count = DB::table('tagihan_santris')->count();
        echo "\nTotal records: $count\n";
        
        if ($count > 0) {
            // Sample data
            $sample = DB::table('tagihan_santris')->limit(3)->get();
            echo "\nSample data:\n";
            foreach ($sample as $row) {
                echo "  - ID: {$row->id}, Santri: {$row->santri_id}, Jenis: {$row->jenis_tagihan_id}, Bulan: " . ($row->bulan ?? 'NULL') . "\n";
            }
        }
    } else {
        echo "tagihan_santris table does NOT exist\n";
        echo "The table name might be 'tagihan_santri' (singular) instead\n";
        
        // Check for singular version
        foreach ($tables as $table) {
            $table_name = array_values((array)$table)[0];
            if ($table_name == 'tagihan_santri') {
                echo "Found 'tagihan_santri' table instead!\n";
                break;
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

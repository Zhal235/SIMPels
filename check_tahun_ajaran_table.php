<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check tahun_ajaran table structure
$columns = DB::select('DESCRIBE tahun_ajaran');
echo "tahun_ajaran table structure:\n";
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

// Sample data
$sample = DB::table('tahun_ajaran')->get();
echo "\nSample tahun_ajaran data:\n";
foreach ($sample as $row) {
    echo "  - ID: {$row->id}\n";
    foreach ((array)$row as $field => $value) {
        if ($field != 'id') {
            $display_value = is_null($value) ? 'NULL' : $value;
            echo "    $field: $display_value\n";
        }
    }
    echo "\n";
}

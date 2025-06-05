<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check santris table structure
$columns = DB::select('DESCRIBE santris');
echo "santris table structure:\n";
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

// Sample data
$sample = DB::table('santris')->first();
echo "\nSample santris data:\n";
if ($sample) {
    foreach ((array)$sample as $field => $value) {
        $display_value = is_null($value) ? 'NULL' : (is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value);
        echo "  - $field: $display_value\n";
    }
}

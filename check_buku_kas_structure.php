<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Buku Kas Table Structure ===\n";

try {
    $columns = DB::select("DESCRIBE buku_kas");
    echo "Buku Kas table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n=== Sample Buku Kas Data ===\n";
    $bukuKas = DB::table('buku_kas')->limit(5)->get();
    foreach ($bukuKas as $item) {
        echo "Data: " . json_encode($item) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Edit Endpoint with Existing ID ===\n";

// Test with existing ID
$jenisTagihan = App\Models\JenisTagihan::first();
if ($jenisTagihan) {
    echo "Testing with ID: {$jenisTagihan->id}\n";
    
    try {
        // Simulate controller edit method
        $controller = new App\Http\Controllers\JenisTagihanController();
        
        // Create a mock request
        $request = new Illuminate\Http\Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        echo "Calling edit method...\n";
        // This won't work directly, but let's try a different approach
        
    } catch (Exception $e) {
        echo "Error in controller test: " . $e->getMessage() . "\n";
    }
}

<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Http\Controllers\JenisTagihanController;
use App\Models\JenisTagihan;
use Illuminate\Http\Request;

// Test method edit langsung dari controller
$controller = new JenisTagihanController();

// Mock request sebagai AJAX
$request = new Request();
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

// Set global request instance
app()->instance('request', $request);

try {
    // Test dengan ID yang ada
    $jenisTagihan = JenisTagihan::first();
    if ($jenisTagihan) {
        echo "Testing with JenisTagihan ID: " . $jenisTagihan->id . " - " . $jenisTagihan->nama . "\n";
        
        $response = $controller->edit($jenisTagihan->id);
        
        // Check if it's JSON response
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            echo "Response Type: JSON\n";
            echo "Status Code: " . $response->getStatusCode() . "\n";
            echo "Content: " . $response->getContent() . "\n";
        } else {
            echo "Response Type: " . get_class($response) . "\n";
            echo "Content: " . $response . "\n";
        }
    } else {
        echo "No JenisTagihan found in database\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

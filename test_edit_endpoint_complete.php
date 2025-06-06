<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Edit Endpoint ===\n";

// Get an existing jenis tagihan ID
$jenisTagihan = App\Models\JenisTagihan::first();

if (!$jenisTagihan) {
    echo "No jenis tagihan found in database!\n";
    exit(1);
}

echo "Testing with existing ID: {$jenisTagihan->id} (Nama: {$jenisTagihan->nama})\n";

// Test the edit endpoint by making a real HTTP request
$url = "http://localhost/SIMPelS/public/keuangan/jenis-tagihan/{$jenisTagihan->id}/edit";

echo "Making AJAX request to: $url\n";

// Create a cURL request to simulate AJAX
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
}

echo "Response:\n";
echo $response . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "\nParsed JSON Response:\n";
        echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        if (isset($data['jenisTagihan'])) {
            echo "Jenis Tagihan ID: " . $data['jenisTagihan']['id'] . "\n";
            echo "Jenis Tagihan Nama: " . $data['jenisTagihan']['nama'] . "\n";
        }
        if (isset($data['bukuKasList'])) {
            echo "Buku Kas Count: " . count($data['bukuKasList']) . "\n";
        }
    } else {
        echo "JSON Parse Error: " . json_last_error_msg() . "\n";
    }
} elseif ($httpCode === 404) {
    echo "404 Error - Route or Data not found\n";
} else {
    echo "HTTP Error Code: $httpCode\n";
}

echo "\n=== Direct Controller Test ===\n";

try {
    // Test controller method directly
    $controller = new App\Http\Controllers\JenisTagihanController();
    
    // Create mock request with AJAX headers
    $request = Illuminate\Http\Request::create("/keuangan/jenis-tagihan/{$jenisTagihan->id}/edit", 'GET');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    $request->headers->set('Accept', 'application/json');
    
    // Mock the request method to return true for AJAX
    app()->instance('request', $request);
    
    $response = $controller->edit($jenisTagihan->id);
    
    if ($response instanceof Illuminate\Http\JsonResponse) {
        echo "Controller returned JSON response\n";
        $data = $response->getData(true);
        echo "Response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Controller returned non-JSON response\n";
        echo "Response type: " . get_class($response) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error testing controller: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

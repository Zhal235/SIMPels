<?php

// Simple test for tunggakan API endpoint
$santriId = 401; // Example santri ID
$url = "http://127.0.0.1:8000/keuangan/pembayaran-santri/tunggakan/$santriId";

echo "Testing URL: $url\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10
    ]
]);

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Failed to fetch data from API\n";
    exit(1);
}

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "ERROR: Invalid JSON response\n";
    echo "Response: $response\n";
    exit(1);
}

echo "SUCCESS: API responded\n";
echo "Data count: " . (is_array($data) ? count($data) : 'Not an array') . "\n";

if (is_array($data) && count($data) > 0) {
    echo "Sample data:\n";
    print_r($data[0]);
} elseif (isset($data['error'])) {
    echo "API Error: " . $data['error'] . "\n";
} else {
    echo "No tunggakan found for santri $santriId\n";
}

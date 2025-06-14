<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;

try {
    echo "=== TEST LOGIN API ===\n\n";

    // Simulasi request login
    $request = new Request();
    $request->merge([
        'email' => 'wali@test.com',
        'password' => 'password123',
        'device_name' => 'test-device'
    ]);

    $authController = new AuthController();
    $response = $authController->login($request);
    
    $responseData = json_decode($response->getContent(), true);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";

    if (isset($responseData['santri'])) {
        echo "\n=== DATA SANTRI DARI API ===\n";
        foreach ($responseData['santri'] as $santri) {
            echo "Nama: " . $santri['nama'] . "\n";
            echo "NIS: " . $santri['nis'] . "\n";
            echo "Kelas: " . $santri['kelas'] . "\n";
            echo "Asrama: " . $santri['asrama'] . "\n";
            echo "---\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

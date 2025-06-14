<?php

// Login untuk mendapatkan token
$loginData = [
    'email' => 'wali@test.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/wali-santri/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginData = json_decode($loginResponse, true);

echo "Login Response:\n";
print_r($loginData);

if (isset($loginData['data']['token'])) {
    $token = $loginData['data']['token'];
    
    // Test API santri
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/wali-santri/santri');
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    
    $santriResponse = curl_exec($ch);
    $santriData = json_decode($santriResponse, true);
    
    echo "\n=== SANTRI API RESPONSE ===\n";
    print_r($santriData);
    
    if (isset($santriData['data']) && count($santriData['data']) > 0) {
        echo "\n=== DETAIL SANTRI ===\n";
        foreach ($santriData['data'] as $santri) {
            echo "ID: " . $santri['id'] . "\n";
            echo "Nama: [" . $santri['nama'] . "]\n";
            echo "NIS: " . $santri['nis'] . "\n";
            echo "---\n";
        }
    }
}

curl_close($ch);

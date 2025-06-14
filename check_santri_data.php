<?php

require_once 'vendor/autoload.php';

use App\Models\Santri;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== CEK DATA SANTRI DI DATABASE ===\n\n";

    // 1. Cek semua santri di database
    $allSantris = Santri::all();
    echo "Total santri di database: " . $allSantris->count() . "\n\n";

    foreach ($allSantris as $santri) {
        echo "ID: {$santri->id}\n";
        echo "NIS: {$santri->nis}\n";
        echo "Nama: {$santri->nama_santri}\n";
        echo "Email Orangtua: " . ($santri->email_orangtua ?? 'tidak ada') . "\n";
        echo "User ID: " . ($santri->user_id ?? 'tidak ada') . "\n";
        echo "Kelas: " . ($santri->kelas ?? 'tidak ada') . "\n";
        echo "---\n";
    }

    // 2. Cek user wali@test.com
    echo "\n=== CEK USER WALI@TEST.COM ===\n";
    $user = User::where('email', 'wali@test.com')->first();
    if ($user) {
        echo "User ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";

        // Cek santri yang terhubung dengan user ini
        echo "\n=== SANTRI YANG TERHUBUNG ===\n";
        $santrisByUserId = Santri::where('user_id', $user->id)->get();
        $santrisByEmail = Santri::where('email_orangtua', $user->email)->get();
        
        echo "Santri by user_id ({$user->id}): " . $santrisByUserId->count() . "\n";
        foreach ($santrisByUserId as $santri) {
            echo "  - {$santri->nama_santri} (NIS: {$santri->nis})\n";
        }
        
        echo "Santri by email_orangtua ({$user->email}): " . $santrisByEmail->count() . "\n";
        foreach ($santrisByEmail as $santri) {
            echo "  - {$santri->nama_santri} (NIS: {$santri->nis})\n";
        }
    } else {
        echo "User tidak ditemukan!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

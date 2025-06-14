<?php

require_once 'vendor/autoload.php';

use App\Models\Santri;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== MEMPERBAIKI KONEKSI USER_ID SANTRI ===\n\n";

    $user = User::where('email', 'wali@test.com')->first();
    if (!$user) {
        echo "❌ User wali@test.com tidak ditemukan\n";
        exit;
    }

    echo "✓ User ditemukan: {$user->name} (ID: {$user->id})\n";

    // Cari santri yang memiliki email_orangtua sama dengan user email
    $santris = Santri::where('email_orangtua', $user->email)->get();
    
    echo "✓ Ditemukan " . $santris->count() . " santri dengan email_orangtua: {$user->email}\n\n";

    foreach ($santris as $santri) {
        echo "Santri: {$santri->nama_santri} (NIS: {$santri->nis})\n";
        echo "  - User ID saat ini: " . ($santri->user_id ?? 'tidak ada') . "\n";
        
        // Update user_id santri agar sesuai dengan user yang benar
        if ($santri->user_id != $user->id) {
            $santri->user_id = $user->id;
            $santri->save();
            echo "  - ✅ User ID diupdate menjadi: {$user->id}\n";
        } else {
            echo "  - ✓ User ID sudah benar\n";
        }
        echo "\n";
    }

    echo "=== VERIFIKASI HASIL ===\n";
    $santrisConnected = Santri::where('user_id', $user->id)
        ->orWhere('email_orangtua', $user->email)
        ->get();
    
    echo "Total santri terhubung dengan user {$user->email}: " . $santrisConnected->count() . "\n";
    foreach ($santrisConnected as $santri) {
        echo "  - {$santri->nama_santri} (NIS: {$santri->nis}, User ID: {$santri->user_id})\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

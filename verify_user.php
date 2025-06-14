<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== VERIFIKASI USER WALI SANTRI ===\n\n";

    $user = User::where('email', 'wali@test.com')->first();
    
    if (!$user) {
        echo "❌ User dengan email wali@test.com tidak ditemukan\n";
        exit;
    }

    echo "✓ User ditemukan: {$user->name} ({$user->email})\n";
    echo "✓ User ID: {$user->id}\n";
    echo "✓ User Type: " . ($user->user_type ?? 'tidak diset') . "\n";
    
    // Cek password
    $passwordValid = Hash::check('password123', $user->password);
    echo "✓ Password valid: " . ($passwordValid ? 'YA' : 'TIDAK') . "\n";
    
    // Cek role
    $hasRole = $user->hasRole('wali_santri');
    echo "✓ Memiliki role 'wali_santri': " . ($hasRole ? 'YA' : 'TIDAK') . "\n";
    
    if ($hasRole) {
        echo "✓ Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    }

    // Cek santri yang terhubung
    $santris = \App\Models\Santri::where('user_id', $user->id)
        ->orWhere('email_orangtua', $user->email)
        ->get();
    
    echo "✓ Jumlah santri terhubung: " . $santris->count() . "\n";
    
    foreach ($santris as $santri) {
        echo "  - {$santri->nama_santri} (NIS: {$santri->nis})\n";
    }

    echo "\n=== STATUS LOGIN ===\n";
    if ($passwordValid && $hasRole) {
        echo "🎉 User siap untuk login di PWA!\n";
        echo "📧 Email: wali@test.com\n";
        echo "🔒 Password: password123\n";
    } else {
        echo "❌ Ada masalah dengan user ini:\n";
        if (!$passwordValid) echo "  - Password tidak valid\n";
        if (!$hasRole) echo "  - Role 'wali_santri' tidak ditemukan\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

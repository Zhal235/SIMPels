<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== RESET PASSWORD USER ===\n\n";

    $user = User::where('email', 'wali@test.com')->first();
    
    if (!$user) {
        echo "❌ User dengan email wali@test.com tidak ditemukan\n";
        exit;
    }

    // Reset password
    $user->password = Hash::make('password123');
    $user->save();

    echo "✓ Password berhasil direset untuk: {$user->email}\n";
    echo "✓ Password baru: password123\n";

    // Verifikasi password
    $passwordValid = Hash::check('password123', $user->password);
    echo "✓ Verifikasi password: " . ($passwordValid ? 'BERHASIL' : 'GAGAL') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

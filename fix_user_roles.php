<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== MEMPERBAIKI ROLE UNTUK USER WALI SANTRI ===\n\n";

    // 1. Cek atau buat role 'wali_santri'
    $role = Role::firstOrCreate(['name' => 'wali_santri', 'guard_name' => 'web']);
    echo "✓ Role 'wali_santri' tersedia\n";

    // 2. Ambil semua user yang memiliki user_type 'wali_santri' tapi belum memiliki role
    $users = User::where('user_type', 'wali_santri')->get();

    if ($users->isEmpty()) {
        echo "⚠️ Tidak ada user dengan user_type 'wali_santri' ditemukan\n";
        
        // Cek user dengan email wali@test.com
        $testUser = User::where('email', 'wali@test.com')->first();
        if ($testUser) {
            echo "✓ User wali@test.com ditemukan, menambahkan role...\n";
            $testUser->assignRole('wali_santri');
            echo "✓ Role 'wali_santri' berhasil ditambahkan ke user wali@test.com\n";
        } else {
            echo "⚠️ User wali@test.com tidak ditemukan\n";
        }
    } else {
        echo "Ditemukan " . $users->count() . " user dengan user_type 'wali_santri'\n\n";

        foreach ($users as $user) {
            if (!$user->hasRole('wali_santri')) {
                $user->assignRole('wali_santri');
                echo "✓ Role 'wali_santri' ditambahkan ke: {$user->email}\n";
            } else {
                echo "- {$user->email} sudah memiliki role 'wali_santri'\n";
            }
        }
    }

    echo "\n=== ROLE ASSIGNMENT SELESAI ===\n";
    echo "Silakan coba login kembali di PWA\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

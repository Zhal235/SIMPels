<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Santri;
use App\Models\Dompet;
use App\Models\WaliSantri;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== MEMBUAT DATA USER WALI SANTRI UNTUK PWA ===\n\n";

    // 1. Cek dan buat santri
    $santri = Santri::where('nis', '2025001')->first();
    if (!$santri) {
        $santri = Santri::create([
            'nis' => '2025001',
            'nama_santri' => 'Ahmad Santri',
            'kelas' => '6A',
            'alamat' => 'Jl. Santri No. 1',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2010-01-01',
            'nama_ayah' => 'Bapak Ahmad',
            'nama_ibu' => 'Ibu Siti',
            'hp_ayah' => '081234567890',
            'email_orangtua' => 'wali@test.com'
        ]);
        echo "✓ Santri created: {$santri->nama_santri}\n";
    } else {
        echo "✓ Santri already exists: {$santri->nama_santri}\n";
        // Update email_orangtua jika perlu
        if ($santri->email_orangtua !== 'wali@test.com') {
            $santri->email_orangtua = 'wali@test.com';
            $santri->save();
            echo "✓ Santri email updated\n";
        }
    }

    // 2. Buat user untuk wali santri  
    $user = User::where('email', 'wali@test.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Bapak Ahmad',
            'email' => 'wali@test.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);
        echo "✓ User created: {$user->email}\n";
    } else {
        echo "✓ User already exists: {$user->email}\n";
    }
    
    // Assign role wali_santri
    if (!$user->hasRole('wali_santri')) {
        $user->assignRole('wali_santri');
        echo "✓ Role 'wali_santri' assigned\n";
    } else {
        echo "✓ User already has 'wali_santri' role\n";
    }

    // 3. Buat dompet santri
    $dompet = Dompet::where('pemilik_id', $santri->id)->where('jenis_pemilik', 'santri')->first();
    if (!$dompet) {
        $nomorDompet = 'DS' . date('YmdHis') . rand(100, 999);
        $dompet = Dompet::create([
            'jenis_pemilik' => 'santri',
            'pemilik_id' => $santri->id,
            'nomor_dompet' => $nomorDompet,
            'saldo' => 500000,
            'limit_transaksi' => 50000,
            'is_active' => true
        ]);
        echo "✓ Dompet created: {$dompet->nomor_dompet}\n";
    } else {
        echo "✓ Dompet already exists: {$dompet->nomor_dompet}\n";
    }

    echo "\n=== DATA BERHASIL DIBUAT ===\n";
    echo "📧 Email: wali@test.com\n";
    echo "🔒 Password: password123\n";
    echo "👨‍🎓 Santri: {$santri->nama_santri} (NIS: {$santri->nis})\n";
    echo "💰 Dompet: {$dompet->nomor_dompet} - Saldo: Rp " . number_format($dompet->saldo) . "\n";
    echo "\n🎉 Silakan login di PWA dengan kredensial di atas!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

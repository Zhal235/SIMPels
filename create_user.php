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

try {    // 1. Cek dan buat santri
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
    }    // 2. Buat wali santri
    $wali = WaliSantri::where('email', 'wali@test.com')->first();
    if (!$wali) {
        $wali = WaliSantri::create([
            'name' => 'Bapak Ahmad',
            'email' => 'wali@test.com',
            'phone' => '081234567890',
            'address' => 'Jl. Santri No. 1',
            'password' => Hash::make('password123')
        ]);
        echo "✓ Wali Santri created: {$wali->name}\n";
    } else {
        echo "✓ Wali Santri already exists: {$wali->name}\n";
    }    // 3. Buat user untuk wali santri  
    $user = User::where('email', 'wali@test.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Bapak Ahmad',
            'email' => 'wali@test.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);
        
        // Assign role wali_santri
        $user->assignRole('wali_santri');
        
        echo "✓ User created: {$user->email}\n";
        echo "✓ Role 'wali_santri' assigned\n";
    } else {
        echo "✓ User already exists: {$user->email}\n";
        
        // Pastikan user memiliki role wali_santri
        if (!$user->hasRole('wali_santri')) {
            $user->assignRole('wali_santri');
            echo "✓ Role 'wali_santri' assigned to existing user\n";
        }
    }

    // 4. Hubungkan santri dengan user
    if ($santri->email_orangtua !== $user->email) {
        $santri->email_orangtua = $user->email;
        $santri->save();
        echo "✓ Santri connected to user\n";
    }

    // 4. Buat dompet santri dengan nomor unik
    $nomorDompet = 'DS' . date('YmdHis');
    $dompet = Dompet::where('pemilik_id', $santri->id)->where('jenis_pemilik', 'santri')->first();
    if (!$dompet) {
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
    }    // 5. Buat tagihan
    $tagihan = TagihanSantri::where('santri_id', $santri->id)->first();
    if (!$tagihan) {
        // Ambil jenis tagihan yang pertama (atau buat default)
        $jenisTagihan = \App\Models\JenisTagihan::first();
        $tahunAjaran = \App\Models\TahunAjaran::where('tahun_mulai', 2024)->first();
        
        if (!$jenisTagihan) {
            $jenisTagihan = \App\Models\JenisTagihan::create([
                'nama' => 'SPP',
                'keterangan' => 'Sumbangan Pembinaan Pendidikan',
                'nominal' => 250000,
                'is_active' => true
            ]);
        }
        
        if (!$tahunAjaran) {
            $tahunAjaran = \App\Models\TahunAjaran::create([
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2025,
                'semester' => 2,
                'is_active' => true
            ]);
        }
        
        $tagihan = TagihanSantri::create([
            'santri_id' => $santri->id,
            'jenis_tagihan_id' => $jenisTagihan->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'bulan' => '2025-01',
            'nominal_tagihan' => 250000,
            'nominal_dibayar' => 0,
            'status' => 'aktif',
            'tanggal_jatuh_tempo' => '2025-01-31',
            'keterangan' => 'SPP Januari 2025'
        ]);
        echo "✓ Tagihan created: {$jenisTagihan->nama} - Rp " . number_format($tagihan->nominal_tagihan) . "\n";
    } else {
        echo "✓ Tagihan already exists\n";
    }    // 6. Buat tunggakan  
    $tunggakan = TunggakanSantri::where('santri_id', $santri->id)->first();
    if (!$tunggakan) {
        $tunggakan = TunggakanSantri::create([
            'santri_id' => $santri->id,
            'jenis_tagihan' => 'SPP',
            'jumlah_tunggakan' => 500000,
            'bulan_tunggakan' => 'November, Desember 2024',
            'tanggal_tunggakan' => '2024-12-31',
            'denda' => 25000,
            'total_tunggakan' => 525000,
            'keterangan' => 'Tunggakan SPP 2 bulan'
        ]);
        echo "✓ Tunggakan created: {$tunggakan->jenis_tagihan} - Rp " . number_format($tunggakan->total_tunggakan) . "\n";
    } else {
        echo "✓ Tunggakan already exists: {$tunggakan->jenis_tagihan}\n";
    }    echo "\n=== DATA BERHASIL DIBUAT ===\n";
    echo "Email: wali@test.com\n";
    echo "Password: password123\n";
    echo "Santri: {$santri->nama_santri} ({$santri->nis})\n";
    echo "Dompet: {$dompet->nomor_dompet} - Saldo: Rp " . number_format($dompet->saldo) . "\n";
    if (isset($tagihan)) {
        echo "Tagihan: Rp " . number_format($tagihan->nominal_tagihan) . "\n";
    }
    if (isset($tunggakan)) {
        echo "Tunggakan: Rp " . number_format($tunggakan->total_tunggakan) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

<?php

use App\Models\Santri;
use App\Models\KelasAnggota;
use App\Models\AsramaAnggota;

echo "=== Verifikasi Relasi Santri Lengkap ===\n";

// Santri dengan kelas dan asrama
$santrisWithRelations = Santri::where('status', 'aktif')
    ->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])
    ->limit(5)
    ->get();

echo "Contoh 5 santri dengan relasi:\n";
foreach ($santrisWithRelations as $santri) {
    $kelas = $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Tidak ada kelas';
    $asrama = $santri->asrama_anggota_terakhir->asrama->nama ?? 'Tidak ada asrama';
    
    echo "- {$santri->nama_santri} (NIS: {$santri->nis})\n";
    echo "  Kelas: {$kelas}\n";
    echo "  Asrama: {$asrama}\n\n";
}

// Summary
$totalSantri = Santri::where('status', 'aktif')->count();
$santriDenganKelas = Santri::where('status', 'aktif')
    ->whereHas('kelasRelasi')
    ->count();
$santriDenganAsrama = Santri::where('status', 'aktif')
    ->whereHas('asrama_anggota_terakhir')
    ->count();

echo "=== Summary ===\n";
echo "Total santri aktif: {$totalSantri}\n";
echo "Santri dengan kelas: {$santriDenganKelas}\n";
echo "Santri dengan asrama: {$santriDenganAsrama}\n";

// Cek apakah semua sudah lengkap
if ($santriDenganKelas == $totalSantri && $santriDenganAsrama == $totalSantri) {
    echo "✅ SEMUA SANTRI SUDAH MEMILIKI KELAS DAN ASRAMA!\n";
} else {
    echo "❌ Ada santri yang belum lengkap relasi kelas/asrama\n";
}

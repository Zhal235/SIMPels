<?php

use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

echo "=== ANALISIS DATA KELAS DAN KELAS_ANGGOTA ===\n\n";

// 1. Cek data kelas_anggota
$totalKelasAnggota = DB::table('kelas_anggota')->count();
echo "1. Total data kelas_anggota: {$totalKelasAnggota}\n";

// 2. Cek data kelas
$totalKelas = Kelas::count();
echo "2. Total data kelas: {$totalKelas}\n";

// 3. Cek contoh data kelas_anggota
if ($totalKelasAnggota > 0) {
    echo "\n3. Contoh 5 data kelas_anggota pertama:\n";
    $contohKelasAnggota = DB::table('kelas_anggota')->limit(5)->get();
    foreach ($contohKelasAnggota as $ka) {
        echo "- Santri ID: {$ka->santri_id}, Kelas ID: {$ka->kelas_id}\n";
    }
} else {
    echo "\n3. TIDAK ADA DATA KELAS_ANGGOTA!\n";
}

// 4. Cek contoh data kelas
if ($totalKelas > 0) {
    echo "\n4. Contoh 5 kelas yang ada:\n";
    $contohKelas = Kelas::limit(5)->get(['id', 'nama']);
    foreach ($contohKelas as $kelas) {
        echo "- Kelas ID: {$kelas->id}, Nama: {$kelas->nama}\n";
    }
} else {
    echo "\n4. TIDAK ADA DATA KELAS!\n";
}

// 5. Cek struktur tabel kelas_anggota
echo "\n5. Struktur tabel kelas_anggota:\n";
$columns = DB::select("DESCRIBE kelas_anggota");
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

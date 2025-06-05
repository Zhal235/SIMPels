<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Tahun Ajaran ===\n";

$tahunAjaran = App\Models\TahunAjaran::getActive();

if ($tahunAjaran) {
    echo "Tahun Ajaran Aktif: " . $tahunAjaran->nama_tahun_ajaran . "\n";
    echo "Tahun Mulai: " . $tahunAjaran->tahun_mulai . "\n";
    echo "Tahun Selesai: " . $tahunAjaran->tahun_selesai . "\n";
    echo "Tanggal Mulai: " . $tahunAjaran->tanggal_mulai . "\n";
    echo "Tanggal Selesai: " . $tahunAjaran->tanggal_selesai . "\n";
} else {
    echo "Tidak ada tahun ajaran aktif\n";
}

echo "\n=== All Tahun Ajaran ===\n";
$allTahunAjaran = App\Models\TahunAjaran::all();
foreach ($allTahunAjaran as $ta) {
    echo "ID: {$ta->id}, Nama: {$ta->nama_tahun_ajaran}, Mulai: {$ta->tahun_mulai}, Selesai: {$ta->tahun_selesai}, Active: " . ($ta->is_active ? 'Yes' : 'No') . "\n";
}

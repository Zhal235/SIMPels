<?php

use App\Models\AsramaAnggota;
use App\Models\Asrama;

echo "=== Verifikasi Data Asrama Anggota ===\n";

// Total asrama anggota
$totalAsramaAnggota = AsramaAnggota::count();
echo "Total asrama anggota: {$totalAsramaAnggota}\n";

// Distribusi per asrama
echo "\nDistribusi per asrama:\n";
$asramas = Asrama::withCount('anggota_asrama')->get();
foreach ($asramas as $asrama) {
    echo "- {$asrama->nama} ({$asrama->kode}): {$asrama->anggota_asrama_count} anggota\n";
}

// Cek beberapa anggota asrama
echo "\nBeberapa anggota asrama:\n";
$anggotaAsrama = AsramaAnggota::with(['santri', 'asrama'])
    ->limit(5)
    ->get();

foreach ($anggotaAsrama as $anggota) {
    echo "- {$anggota->santri->nama_santri} ({$anggota->santri->nis}) di {$anggota->asrama->nama}\n";
}

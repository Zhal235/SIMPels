<?php
// Script untuk menganalisis masalah santri tanpa kelas dan tagihan

use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;

echo "=== ANALISIS SANTRI TANPA KELAS DAN TAGIHAN ===\n\n";

$tahunAjaran = TahunAjaran::getActive();
echo "Tahun Ajaran Aktif: {$tahunAjaran->nama_tahun_ajaran}\n\n";

// 1. Cek santri yang tidak punya kelas aktif
$santriTanpaKelas = Santri::whereDoesntHave('kelasRelasi')
    ->where('status', 'aktif')
    ->count();
echo "1. Santri aktif tanpa kelas: {$santriTanpaKelas}\n";

// 2. Cek santri yang punya tagihan tapi tidak punya kelas
$santriPunyaTagihanTanpaKelas = Santri::whereHas('tagihanSantris')
    ->whereDoesntHave('kelasRelasi')
    ->where('status', 'aktif')
    ->count();
echo "2. Santri punya tagihan tapi tanpa kelas: {$santriPunyaTagihanTanpaKelas}\n";

// 3. Cek total tagihan untuk santri tanpa kelas
$totalTagihanTanpaKelas = TagihanSantri::whereHas('santri', function($q) {
    $q->whereDoesntHave('kelasRelasi')->where('status', 'aktif');
})->count();
echo "3. Total tagihan untuk santri tanpa kelas: {$totalTagihanTanpaKelas}\n";

// 4. Detail santri tanpa kelas (ambil 10 contoh)
echo "\n=== DETAIL SANTRI TANPA KELAS (10 contoh) ===\n";
$contohSantriTanpaKelas = Santri::whereDoesntHave('kelasRelasi')
    ->where('status', 'aktif')
    ->with(['tagihanSantris' => function($q) use ($tahunAjaran) {
        $q->where('tahun_ajaran_id', $tahunAjaran->id);
    }])
    ->limit(10)
    ->get();

foreach ($contohSantriTanpaKelas as $santri) {
    $jumlahTagihan = $santri->tagihanSantris->count();
    echo "- {$santri->nama_santri} (NIS: {$santri->nis}) - Tagihan: {$jumlahTagihan}\n";
}

// 5. Cek total santri aktif
$totalSantriAktif = Santri::where('status', 'aktif')->count();
echo "\n4. Total santri aktif: {$totalSantriAktif}\n";

// 6. Cek santri dengan kelas aktif
$santriDenganKelas = Santri::whereHas('kelasRelasi')
    ->where('status', 'aktif')
    ->count();
echo "5. Santri aktif dengan kelas: {$santriDenganKelas}\n";

// 7. Cek total tagihan tahun ajaran ini
$totalTagihan = TagihanSantri::where('tahun_ajaran_id', $tahunAjaran->id)->count();
echo "6. Total tagihan tahun ajaran ini: {$totalTagihan}\n";

echo "\n=== KESIMPULAN ===\n";
if ($santriTanpaKelas > 0 && $totalTagihanTanpaKelas == 0) {
    echo "❌ MASALAH: Ada {$santriTanpaKelas} santri tanpa kelas yang tidak punya tagihan!\n";
    echo "   Ini menunjukkan generate tagihan tidak berjalan untuk santri tanpa kelas.\n";
} elseif ($santriTanpaKelas > 0 && $totalTagihanTanpaKelas > 0) {
    echo "✅ BAIK: Santri tanpa kelas tetap punya tagihan.\n";
    echo "   Tapi perlu dipastikan mereka bisa bayar meski tanpa kelas.\n";
} else {
    echo "✅ NORMAL: Semua santri aktif punya kelas.\n";
}

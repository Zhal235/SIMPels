<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\KelasAnggota;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG TAGIHAN SANTRI TANPA KELAS ===\n\n";

// Ambil tahun ajaran aktif
$activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
echo "Tahun Ajaran Aktif: " . ($activeTahunAjaran ? $activeTahunAjaran->nama_tahun_ajaran : 'TIDAK ADA') . "\n\n";

if (!$activeTahunAjaran) {
    die("Tidak ada tahun ajaran aktif!\n");
}

// Cari santri aktif yang tidak punya kelas
$santrisWithoutClass = Santri::where('status', 'aktif')
    ->with(['kelasRelasi'])
    ->get()
    ->filter(function($santri) {
        return $santri->kelasRelasi->isEmpty();
    });

echo "Jumlah santri aktif tanpa kelas: " . $santrisWithoutClass->count() . "\n\n";

if ($santrisWithoutClass->count() > 0) {
    echo "=== DETAIL SANTRI TANPA KELAS ===\n";
    foreach ($santrisWithoutClass->take(5) as $santri) {
        echo "- ID: {$santri->id}, NIS: {$santri->nis}, Nama: {$santri->nama_santri}\n";
        
        // Cek tagihan untuk santri ini
        $tagihanCount = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->count();
        
        echo "  Jumlah tagihan: {$tagihanCount}\n";
        
        if ($tagihanCount > 0) {
            $tagihans = TagihanSantri::where('santri_id', $santri->id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->with('jenisTagihan')
                ->get();
            
            foreach ($tagihans as $tagihan) {
                echo "  - {$tagihan->jenisTagihan->nama}: Rp " . number_format($tagihan->nominal_tagihan) . "\n";
            }
        }
        echo "\n";
    }
}

// Cek total santri aktif
$totalSantriAktif = Santri::where('status', 'aktif')->count();
echo "Total santri aktif: {$totalSantriAktif}\n";

// Cek total santri dengan kelas
$santriWithClass = Santri::where('status', 'aktif')
    ->with(['kelasRelasi'])
    ->get()
    ->filter(function($santri) {
        return !$santri->kelasRelasi->isEmpty();
    });

echo "Santri aktif dengan kelas: " . $santriWithClass->count() . "\n";
echo "Santri aktif tanpa kelas: " . $santrisWithoutClass->count() . "\n\n";

// Cek jenis tagihan yang ada
$jenisTagihans = JenisTagihan::all();
echo "=== JENIS TAGIHAN YANG ADA ===\n";
foreach ($jenisTagihans as $jenis) {
    echo "- {$jenis->nama} ({$jenis->kategori_tagihan}): Rp " . number_format($jenis->nominal) . "\n";
    echo "  is_nominal_per_kelas: " . ($jenis->is_nominal_per_kelas ? 'Ya' : 'Tidak') . "\n";
    echo "  is_bulanan: " . ($jenis->is_bulanan ? 'Ya' : 'Tidak') . "\n\n";
}

echo "\n=== SELESAI ===\n";

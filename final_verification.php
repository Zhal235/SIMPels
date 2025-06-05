<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;

// Initialize database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'simpels_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== FINAL VERIFICATION REPORT ===\n\n";

// 1. Check tahun ajaran aktif
echo "1. TAHUN AJARAN AKTIF:\n";
$tahunAjaran = TahunAjaran::where('is_active', 1)->first();
echo "   - Nama: {$tahunAjaran->nama}\n";
echo "   - Periode: {$tahunAjaran->tanggal_mulai} s/d {$tahunAjaran->tanggal_selesai}\n";

// 2. Check format bulan
echo "\n2. FORMAT BULAN TAGIHAN:\n";
$bulanUnique = TagihanSantri::distinct()->pluck('bulan')->sort()->values();
echo "   - Jumlah bulan unik: " . $bulanUnique->count() . "\n";
echo "   - Format bulan: " . $bulanUnique->join(', ') . "\n";

// 3. Check format validation
echo "\n3. VALIDASI FORMAT:\n";
$correctFormat = $bulanUnique->every(function($bulan) {
    return preg_match('/^\d{4}-\d{2}$/', $bulan);
});
echo "   - Semua bulan menggunakan format YYYY-MM: " . ($correctFormat ? "✓ YA" : "✗ TIDAK") . "\n";

// 4. Check jenis tagihan distribution
echo "\n4. DISTRIBUSI JENIS TAGIHAN:\n";
$jenisTagihan = JenisTagihan::all();
foreach($jenisTagihan as $jenis) {
    $count = TagihanSantri::where('jenis_tagihan_id', $jenis->id)->count();
    $totalNominal = TagihanSantri::where('jenis_tagihan_id', $jenis->id)->sum('nominal_tagihan');
    echo "   - {$jenis->nama}: {$count} tagihan, Total: Rp " . number_format($totalNominal) . "\n";
}

// 5. Check monthly distribution
echo "\n5. DISTRIBUSI BULANAN:\n";
$monthlyStats = TagihanSantri::selectRaw('bulan, COUNT(*) as count, SUM(nominal_tagihan) as total')
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();

foreach($monthlyStats as $stat) {
    echo "   - {$stat->bulan}: {$stat->count} tagihan, Total: Rp " . number_format($stat->total) . "\n";
}

// 6. Check for problematic data
echo "\n6. PENGECEKAN DATA BERMASALAH:\n";
$issues = [
    'Bulan kosong atau NULL' => TagihanSantri::where('bulan', '')->orWhereNull('bulan')->count(),
    'Bulan dengan format lama (contoh: 0-01)' => TagihanSantri::where('bulan', 'like', '0-%')->count(),
    'Bulan hanya tahun (contoh: 2024)' => TagihanSantri::where('bulan', 'regexp', '^[0-9]{4}$')->count(),
    'Nominal tagihan 0 atau NULL' => TagihanSantri::where('nominal_tagihan', 0)->orWhereNull('nominal_tagihan')->count(),
];

$hasIssues = false;
foreach($issues as $issue => $count) {
    if($count > 0) {
        echo "   ✗ {$issue}: {$count}\n";
        $hasIssues = true;
    }
}

if(!$hasIssues) {
    echo "   ✓ Tidak ada data bermasalah ditemukan\n";
}

// 7. Total summary
echo "\n7. RINGKASAN TOTAL:\n";
$totalTagihan = TagihanSantri::count();
$totalNominal = TagihanSantri::sum('nominal_tagihan');
echo "   - Total tagihan: " . number_format($totalTagihan) . "\n";
echo "   - Total nominal: Rp " . number_format($totalNominal) . "\n";

echo "\n=== KESIMPULAN ===\n";
if($correctFormat && !$hasIssues) {
    echo "✓ BERHASIL: Semua tagihan sudah menggunakan format bulan yang benar (YYYY-MM)\n";
    echo "✓ BERHASIL: Tidak ada data bermasalah yang ditemukan\n";
    echo "✓ BERHASIL: Proses generate tagihan santri sudah berfungsi dengan baik\n";
} else {
    echo "✗ PERLU DIPERBAIKI: Masih ada masalah yang perlu diselesaikan\n";
}

echo "\n=== SELESAI ===\n";

?>

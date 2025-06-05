<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICATION: PAYMENT CATEGORIES ===\n\n";

// 1. Check active tahun ajaran
echo "1. CHECKING ACTIVE TAHUN AJARAN:\n";
$activeTahunAjaran = TahunAjaran::getActive();
if ($activeTahunAjaran) {
    echo "   ✓ Active Tahun Ajaran: {$activeTahunAjaran->nama} (ID: {$activeTahunAjaran->id})\n";
} else {
    echo "   ❌ No active tahun ajaran found!\n";
    exit(1);
}

echo "\n";

// 2. Check jenis tagihan categories
echo "2. CHECKING JENIS TAGIHAN CATEGORIES:\n";
$jenisTagihans = JenisTagihan::all();
echo "   Total Jenis Tagihan: " . $jenisTagihans->count() . "\n";

foreach ($jenisTagihans as $jenis) {
    $kategori = $jenis->kategori_tagihan ?? 'NULL';
    $isBulanan = $jenis->is_bulanan ? 'YES' : 'NO';
    echo "   - {$jenis->nama}: Kategori={$kategori}, Bulanan={$isBulanan}\n";
}

echo "\n";

// 3. Check tagihan santri data with categories
echo "3. CHECKING TAGIHAN SANTRI WITH CATEGORIES:\n";
$tagihanSantri = TagihanSantri::where('tahun_ajaran_id', $activeTahunAjaran->id)
    ->with('jenisTagihan')
    ->take(10)
    ->get();

echo "   Sample Tagihan Santri (first 10):\n";
foreach ($tagihanSantri as $tagihan) {
    $kategori = $tagihan->jenisTagihan->kategori_tagihan ?? 'NULL';
    $isBulanan = $tagihan->jenisTagihan->is_bulanan ? 'YES' : 'NO';
    echo "   - ID:{$tagihan->id} | {$tagihan->jenisTagihan->nama} | Bulan:{$tagihan->bulan} | Kategori:{$kategori} | Bulanan:{$isBulanan}\n";
}

echo "\n";

// 4. Count by categories
echo "4. COUNTING BY CATEGORIES:\n";
$rutinCount = TagihanSantri::where('tahun_ajaran_id', $activeTahunAjaran->id)
    ->whereHas('jenisTagihan', function($q) {
        $q->where('kategori_tagihan', 'Rutin')->orWhere('is_bulanan', true);
    })
    ->count();

$insidentilCount = TagihanSantri::where('tahun_ajaran_id', $activeTahunAjaran->id)
    ->whereHas('jenisTagihan', function($q) {
        $q->where('kategori_tagihan', 'Insidentil');
    })
    ->count();

echo "   ✓ Tagihan Rutin/Bulanan: {$rutinCount}\n";
echo "   ✓ Tagihan Insidentil: {$insidentilCount}\n";

echo "\n";

// 5. Check specific santri data
echo "5. CHECKING SPECIFIC SANTRI DATA:\n";
$santri = \App\Models\Santri::where('status', 'aktif')->first();
if ($santri) {
    echo "   Testing dengan Santri: {$santri->nama_santri} (ID: {$santri->id})\n";
    
    $tagihanSantriSpecific = TagihanSantri::where('santri_id', $santri->id)
        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
        ->with('jenisTagihan')
        ->get();
    
    echo "   Total tagihan untuk santri ini: " . $tagihanSantriSpecific->count() . "\n";
    
    $rutinSpecific = $tagihanSantriSpecific->filter(function($t) {
        return $t->jenisTagihan->kategori_tagihan === 'Rutin' || $t->jenisTagihan->is_bulanan;
    })->count();
    
    $insidentilSpecific = $tagihanSantriSpecific->filter(function($t) {
        return $t->jenisTagihan->kategori_tagihan === 'Insidentil';
    })->count();
    
    echo "   - Rutin/Bulanan: {$rutinSpecific}\n";
    echo "   - Insidentil: {$insidentilSpecific}\n";
    
    // Show sample by category
    echo "\n   Sample Rutin/Bulanan:\n";
    $rutinSample = $tagihanSantriSpecific->filter(function($t) {
        return $t->jenisTagihan->kategori_tagihan === 'Rutin' || $t->jenisTagihan->is_bulanan;
    })->take(3);
    
    foreach ($rutinSample as $tagihan) {
        echo "   - {$tagihan->jenisTagihan->nama} | {$tagihan->bulan} | Status: {$tagihan->status}\n";
    }
    
    echo "\n   Sample Insidentil:\n";
    $insidentilSample = $tagihanSantriSpecific->filter(function($t) {
        return $t->jenisTagihan->kategori_tagihan === 'Insidentil';
    })->take(3);
    
    foreach ($insidentilSample as $tagihan) {
        echo "   - {$tagihan->jenisTagihan->nama} | {$tagihan->bulan} | Status: {$tagihan->status}\n";
    }
}

echo "\n";

// 6. Check month format
echo "6. CHECKING MONTH FORMAT:\n";
$monthSample = TagihanSantri::where('tahun_ajaran_id', $activeTahunAjaran->id)
    ->select('bulan')
    ->distinct()
    ->get()
    ->pluck('bulan')
    ->sort();

echo "   Unique month formats found:\n";
foreach ($monthSample as $month) {
    echo "   - '{$month}'\n";
}

$correctFormat = $monthSample->filter(function($month) {
    return preg_match('/^\d{4}-\d{2}$/', $month);
});

echo "\n   ✓ Months with correct YYYY-MM format: " . $correctFormat->count() . "/" . $monthSample->count() . "\n";

if ($correctFormat->count() === $monthSample->count()) {
    echo "   ✅ All months are in correct format!\n";
} else {
    echo "   ⚠️  Some months may need format correction\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";

?>

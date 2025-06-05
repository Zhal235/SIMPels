<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Santri;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATING SAMPLE INSIDENTIL PAYMENTS ===\n\n";

// Get active tahun ajaran
$activeTahunAjaran = TahunAjaran::getActive();
if (!$activeTahunAjaran) {
    echo "❌ No active tahun ajaran found!\n";
    exit(1);
}

echo "✓ Using Tahun Ajaran: {$activeTahunAjaran->nama} (ID: {$activeTahunAjaran->id})\n";

// Get jenis tagihan insidentil
$jenisTagihanInsidentil = JenisTagihan::where('kategori_tagihan', 'Insidentil')->get();
echo "✓ Found " . $jenisTagihanInsidentil->count() . " insidentil jenis tagihan\n";

if ($jenisTagihanInsidentil->count() === 0) {
    echo "❌ No insidentil jenis tagihan found! Please create some first.\n";
    exit(1);
}

// Get some santri for testing
$santris = Santri::where('status', 'aktif')->take(5)->get();
echo "✓ Will create for " . $santris->count() . " santri\n\n";

$created = 0;
$skipped = 0;

foreach ($santris as $santri) {
    echo "Processing Santri: {$santri->nama_santri} (ID: {$santri->id})\n";
    
    foreach ($jenisTagihanInsidentil as $jenisTagihan) {
        // Create for a few different months
        $months = ['2024-07', '2024-09', '2024-11', '2025-01', '2025-03'];
        
        foreach ($months as $month) {
            // Check if already exists
            $exists = TagihanSantri::where('santri_id', $santri->id)
                ->where('jenis_tagihan_id', $jenisTagihan->id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->where('bulan', $month)
                ->exists();
            
            if ($exists) {
                $skipped++;
                continue;
            }
            
            // Create tagihan
            TagihanSantri::create([
                'santri_id' => $santri->id,
                'jenis_tagihan_id' => $jenisTagihan->id,
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'bulan' => $month,
                'nominal_tagihan' => $jenisTagihan->nominal,
                'nominal_dibayar' => 0,
                'sisa_tagihan' => $jenisTagihan->nominal,
                'status' => 'aktif',
                'tanggal_jatuh_tempo' => now()->addDays(30)->format('Y-m-d'),
                'keterangan' => "Tagihan {$jenisTagihan->nama} untuk bulan " . $month
            ]);
            
            $created++;
            echo "  ✓ Created: {$jenisTagihan->nama} - {$month}\n";
        }
    }
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "✓ Created: {$created} new tagihan\n";
echo "⏭ Skipped: {$skipped} existing tagihan\n";

// Verify results
echo "\n=== VERIFICATION ===\n";
$totalInsidentil = TagihanSantri::where('tahun_ajaran_id', $activeTahunAjaran->id)
    ->whereHas('jenisTagihan', function($q) {
        $q->where('kategori_tagihan', 'Insidentil');
    })
    ->count();

echo "✓ Total Insidentil tagihan in database: {$totalInsidentil}\n";

// Show sample for first santri
$firstSantri = $santris->first();
$sampleTagihan = TagihanSantri::where('santri_id', $firstSantri->id)
    ->where('tahun_ajaran_id', $activeTahunAjaran->id)
    ->whereHas('jenisTagihan', function($q) {
        $q->where('kategori_tagihan', 'Insidentil');
    })
    ->with('jenisTagihan')
    ->take(3)
    ->get();

echo "\nSample tagihan for {$firstSantri->nama_santri}:\n";
foreach ($sampleTagihan as $tagihan) {
    echo "- {$tagihan->jenisTagihan->nama} | {$tagihan->bulan} | Rp " . number_format($tagihan->nominal_tagihan) . "\n";
}

echo "\n=== COMPLETE ===\n";

?>

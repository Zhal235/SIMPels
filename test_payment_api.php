<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;

echo "=== PAYMENT API TEST ===\n\n";

try {    // Get active tahun ajaran
    $activeTahunAjaran = TahunAjaran::where('is_active', 1)->first();
    if (!$activeTahunAjaran) {
        echo "ERROR: No active tahun ajaran found\n";
        exit;
    }
    
    echo "Active Tahun Ajaran: {$activeTahunAjaran->nama_tahun_ajaran} (ID: {$activeTahunAjaran->id})\n\n";
    
    // Get first santri
    $santri = DB::table('santris')->first();
    echo "Testing with Santri: {$santri->nama_santri} (ID: {$santri->id})\n\n";
    
    // Simulate the controller logic
    $tagihanSantri = TagihanSantri::where('santri_id', $santri->id)
        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
        ->with(['jenisTagihan', 'tahunAjaran', 'transaksis'])
        ->orderBy('jenis_tagihan_id')
        ->get();
    
    echo "Total Tagihan Found: " . $tagihanSantri->count() . "\n\n";
    
    // Group by category for display
    $rutin_count = 0;
    $insidental_count = 0;
    
    $sample_payments = $tagihanSantri->map(function ($tagihan) use (&$rutin_count, &$insidental_count) {
        $category = $tagihan->jenisTagihan->kategori_tagihan ?? 'Rutin';
        if ($category == 'Rutin') $rutin_count++;
        else $insidental_count++;
        
        return [
            'id' => $tagihan->id,
            'jenis_tagihan' => $tagihan->jenisTagihan->nama,
            'jenis_tagihan_id' => $tagihan->jenis_tagihan_id,
            'bulan' => $tagihan->bulan,
            'nominal_tagihan' => $tagihan->nominal_tagihan,
            'nominal_dibayar' => $tagihan->nominal_dibayar,
            'status_pembayaran' => $tagihan->status,
            'kategori_tagihan' => $category,
            'is_bulanan' => $tagihan->jenisTagihan->is_bulanan ?? false,
        ];
    });
    
    echo "Rutin Tagihan: $rutin_count\n";
    echo "Insidental Tagihan: $insidental_count\n\n";
    
    echo "Sample API Response Structure:\n";
    echo "------------------------------\n";
    
    // Show first few records for each category
    $rutin_samples = $sample_payments->where('kategori_tagihan', 'Rutin')->take(3);
    $insidental_samples = $sample_payments->where('kategori_tagihan', 'Insidental')->take(3);
    
    echo "RUTIN SAMPLES:\n";
    foreach ($rutin_samples as $sample) {
        echo "  - {$sample['jenis_tagihan']} | {$sample['bulan']} | Rp " . number_format($sample['nominal_tagihan']) . " | Status: {$sample['status_pembayaran']}\n";
    }
    
    echo "\nINSIDENTAL SAMPLES:\n";
    foreach ($insidental_samples as $sample) {
        echo "  - {$sample['jenis_tagihan']} | {$sample['bulan']} | Rp " . number_format($sample['nominal_tagihan']) . " | Status: {$sample['status_pembayaran']}\n";
    }
    
    echo "\n=== API TEST SUCCESSFUL ===\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

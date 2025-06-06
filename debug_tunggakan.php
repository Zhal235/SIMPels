<?php

require_once 'bootstrap/app.php';

use App\Models\TagihanSantri;
use App\Models\TahunAjaran;
use App\Models\Santri;

echo "=== Debug Tunggakan Endpoint ===\n";

// Get active tahun ajaran
$activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
echo "Active tahun ajaran: " . ($activeTahunAjaran ? $activeTahunAjaran->nama : 'None') . "\n";
echo "Active tahun ajaran ID: " . ($activeTahunAjaran ? $activeTahunAjaran->id : 'None') . "\n";

// Get a sample santri
$santri = Santri::first();
echo "Sample santri: " . ($santri ? $santri->nama . " (ID: {$santri->id})" : 'None') . "\n";

if ($santri && $activeTahunAjaran) {
    echo "\n=== Testing Query Logic ===\n";
    
    $today = now()->format('Y-m-d');
    echo "Today: $today\n";
    
    // Test tunggakan query (same as in controller)
    $tunggakanQuery = TagihanSantri::where('santri_id', $santri->id)
        ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
        ->where(function($query) use ($today, $activeTahunAjaran) {
            // Tagihan yang sudah jatuh tempo
            $query->where('tanggal_jatuh_tempo', '<', $today)
                  // Atau tagihan dari tahun ajaran sebelumnya
                  ->orWhere('tahun_ajaran_id', '!=', $activeTahunAjaran->id);
        });
    
    $tunggakanCount = $tunggakanQuery->count();
    echo "Tunggakan count for this santri: $tunggakanCount\n";
    
    if ($tunggakanCount > 0) {
        echo "\n=== Sample Tunggakan Data ===\n";
        $samples = $tunggakanQuery->with(['jenisTagihan', 'tahunAjaran'])->take(3)->get();
        
        foreach ($samples as $sample) {
            echo "ID: {$sample->id}, Bulan: {$sample->bulan}, Due: {$sample->tanggal_jatuh_tempo}, ";
            echo "Tahun: " . ($sample->tahunAjaran ? $sample->tahunAjaran->nama : 'N/A') . ", ";
            echo "Sisa: {$sample->sisa_tagihan}\n";
        }
    }
    
    // Test current payments query (tab rutin)
    echo "\n=== Testing Current Payments Query ===\n";
    $currentQuery = TagihanSantri::where('santri_id', $santri->id)
        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
        ->where(function($query) use ($today) {
            $query->whereNull('tanggal_jatuh_tempo')
                  ->orWhere('tanggal_jatuh_tempo', '>=', $today);
        });
    
    $currentCount = $currentQuery->count();
    echo "Current payments count for this santri: $currentCount\n";
}

echo "\n=== Tahun Ajaran Data ===\n";
$tahunAjarans = TahunAjaran::all();
foreach ($tahunAjarans as $tahun) {
    echo "ID: {$tahun->id}, Nama: " . ($tahun->nama ?? 'NULL') . ", Active: " . ($tahun->is_active ? 'Yes' : 'No') . "\n";
}

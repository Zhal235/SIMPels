<?php
// Test script untuk endpoint tunggakan
require_once 'vendor/autoload.php';

use App\Models\Santri;
use App\Models\TagihanSantri;

// Get sample santri with unpaid bills (tunggakan)
$santriWithTunggakan = Santri::whereHas('tagihanSantris', function($query) {
    $query->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan');
})->first();

if ($santriWithTunggakan) {
    echo "Sample Santri with Tunggakan:\n";
    echo "ID: " . $santriWithTunggakan->id . "\n";
    echo "Nama: " . $santriWithTunggakan->nama . "\n";
    
    // Count tunggakan for this santri
    $tunggakanCount = TagihanSantri::where('santri_id', $santriWithTunggakan->id)
        ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
        ->count();
        
    echo "Jumlah Tunggakan: " . $tunggakanCount . "\n";
    
    // Test URL
    echo "\nTest URL: http://127.0.0.1:8000/keuangan/pembayaran-santri/tunggakan/" . $santriWithTunggakan->id . "\n";
} else {
    echo "No santri with tunggakan found\n";
}
?>

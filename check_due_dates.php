<?php

require_once 'bootstrap/app.php';

use App\Models\TagihanSantri;
use App\Models\TahunAjaran;

echo "=== Checking Due Dates After Update ===\n";
echo "Current Date: " . now()->format('Y-m-d') . "\n\n";

// Check sample data
echo "=== Sample Data ===\n";
$samples = TagihanSantri::with(['jenisTagihan', 'tahunAjaran'])
    ->take(5)
    ->get();

foreach($samples as $sample) {
    $tahunNama = $sample->tahunAjaran ? $sample->tahunAjaran->nama : 'N/A';
    $isOverdue = $sample->tanggal_jatuh_tempo < now()->format('Y-m-d') ? 'OVERDUE' : 'OK';
    echo "ID: {$sample->id}, Bulan: {$sample->bulan}, Due: {$sample->tanggal_jatuh_tempo}, Tahun: {$tahunNama}, Status: {$isOverdue}\n";
}

// Check sample current/future data
echo "\n=== Sample Current/Future Data ===\n";
$currentSamples = TagihanSantri::with(['jenisTagihan', 'tahunAjaran'])
    ->where('tanggal_jatuh_tempo', '>=', now()->format('Y-m-d'))
    ->take(5)
    ->get();

foreach($currentSamples as $sample) {
    $tahunNama = $sample->tahunAjaran ? $sample->tahunAjaran->nama : 'N/A';
    echo "ID: {$sample->id}, Bulan: {$sample->bulan}, Due: {$sample->tanggal_jatuh_tempo}, Tahun: {$tahunNama}\n";
}

// Check counts
$totalOverdue = TagihanSantri::where('tanggal_jatuh_tempo', '<', now()->format('Y-m-d'))->count();
$totalCurrent = TagihanSantri::where('tanggal_jatuh_tempo', '>=', now()->format('Y-m-d'))->count();

echo "\n=== Counts ===\n";
echo "Overdue: $totalOverdue\n";
echo "Current/Future: $totalCurrent\n";

// Check active year
$activeYear = TahunAjaran::where('is_active', true)->first();
echo "\n=== Active Year ===\n";
echo "Active Year: " . ($activeYear ? $activeYear->nama : 'None') . "\n";
if ($activeYear) {
    echo "Start Year: {$activeYear->tahun_mulai}\n";
    echo "End Year: {$activeYear->tahun_selesai}\n";
}

?>

<?php

use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;

echo "=== Filling Due Dates for Existing Data ===\n";

// Function to calculate due date (sama seperti di TagihanService)
function calculateDueDate($jenisTagihan, $tahunAjaran, $bulan = null) {
    if ($jenisTagihan->is_bulanan && $bulan) {
        // Bulan format: YYYY-MM
        $bulanParts = explode('-', $bulan);
        if (count($bulanParts) == 2) {
            $year = (int) $bulanParts[0];
            $month = (int) $bulanParts[1];
            
            return Carbon::createFromDate($year, $month, 10);
        }
    }
    
    // Untuk tagihan tahunan, set ke 3 bulan setelah tahun mulai
    return Carbon::createFromDate($tahunAjaran->tahun_mulai, 10, 1); // October 1st
}

// Get all TagihanSantri (reset all)
$tagihanSantris = TagihanSantri::with(['jenisTagihan', 'tahunAjaran'])
    ->get();

echo "Found " . $tagihanSantris->count() . " records to update\n";

$updated = 0;
$errors = 0;

foreach ($tagihanSantris as $tagihan) {
    try {
        if ($tagihan->jenisTagihan && $tagihan->tahunAjaran) {
            $dueDate = calculateDueDate($tagihan->jenisTagihan, $tagihan->tahunAjaran, $tagihan->bulan);
            
            $tagihan->tanggal_jatuh_tempo = $dueDate;
            $tagihan->save();
            
            $updated++;
            
            if ($updated % 100 == 0) {
                echo "Updated $updated records...\n";
            }
        } else {
            $errors++;
            echo "Error: Missing jenisTagihan or tahunAjaran for ID " . $tagihan->id . "\n";
        }
    } catch (Exception $e) {
        $errors++;
        echo "Error updating ID " . $tagihan->id . ": " . $e->getMessage() . "\n";
    }
}

echo "\n=== Update Complete ===\n";
echo "Updated: $updated records\n";
echo "Errors: $errors records\n";

// Verify results
$withDueDate = TagihanSantri::whereNotNull('tanggal_jatuh_tempo')->count();
$withoutDueDate = TagihanSantri::whereNull('tanggal_jatuh_tempo')->count();

echo "\nCurrent state:\n";
echo "With due date: $withDueDate\n";
echo "Without due date: $withoutDueDate\n";

?>

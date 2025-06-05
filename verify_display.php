<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TagihanSantri;
use App\Models\Santri;
use App\Models\TahunAjaran;

echo "=== Verifikasi Tampilan Detail Tagihan Rutin per Bulan ===\n";

// Get one sample student
$santri = Santri::where('status', 'aktif')->with('kelasRelasi')->first();
if (!$santri) {
    echo "Tidak ada santri aktif ditemukan.\n";
    exit;
}

echo "Sample Santri: {$santri->nama_santri} (ID: {$santri->id})\n";
echo "Kelas: " . $santri->kelasRelasi->pluck('nama')->join(', ') . "\n";

// Get active academic year
$tahunAjaran = TahunAjaran::where('is_active', true)->first();
echo "Tahun Ajaran: {$tahunAjaran->nama_tahun_ajaran}\n\n";

// Get recurring bills for this student (similar to controller logic)
$tagihanRutin = TagihanSantri::where('santri_id', $santri->id)
    ->whereHas('jenisTagihan', function($query) {
        $query->where('kategori_tagihan', 'Rutin');
    })
    ->with(['jenisTagihan', 'transaksis'])
    ->get();

echo "--- Tagihan Rutin ---\n";
echo "Total tagihan rutin: " . $tagihanRutin->count() . "\n";

if ($tagihanRutin->count() > 0) {
    // Group by month-year for better display
    $groupedByMonth = $tagihanRutin->groupBy('bulan_tagihan');
    
    echo "\nPengelompokan per bulan:\n";
    foreach ($groupedByMonth as $bulan => $tagihan) {
        echo "Bulan: $bulan\n";
        foreach ($tagihan as $item) {
            $totalDibayar = $item->transaksis->sum('nominal_pembayaran');
            $sisaBayar = max(0, $item->nominal_tagihan - $totalDibayar);
            
            echo "  - {$item->jenisTagihan->nama}: Rp " . number_format($item->nominal_tagihan, 0, ',', '.') . 
                 " | Dibayar: Rp " . number_format($totalDibayar, 0, ',', '.') . 
                 " | Sisa: Rp " . number_format($sisaBayar, 0, ',', '.') . "\n";
        }
        echo "\n";
    }
} else {
    echo "Tidak ada tagihan rutin untuk santri ini.\n";
}

// Get incidental bills
$tagihanInsidentil = TagihanSantri::where('santri_id', $santri->id)
    ->whereHas('jenisTagihan', function($query) {
        $query->where('kategori_tagihan', 'Insidental');
    })
    ->with(['jenisTagihan', 'transaksis'])
    ->get();

echo "--- Tagihan Insidentil ---\n";
echo "Total tagihan insidentil: " . $tagihanInsidentil->count() . "\n";

if ($tagihanInsidentil->count() > 0) {
    foreach ($tagihanInsidentil as $item) {
        $totalDibayar = $item->transaksis->sum('nominal_pembayaran');
        $sisaBayar = max(0, $item->nominal_tagihan - $totalDibayar);
        
        echo "- {$item->jenisTagihan->nama}: Rp " . number_format($item->nominal_tagihan, 0, ',', '.') . 
             " | Dibayar: Rp " . number_format($totalDibayar, 0, ',', '.') . 
             " | Sisa: Rp " . number_format($sisaBayar, 0, ',', '.') . "\n";
    }
}

echo "\n=== Verifikasi Selesai ===\n";

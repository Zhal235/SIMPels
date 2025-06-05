<?php
require 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;

echo "=== DEBUG TAGIHAN INSIDENTIL ===\n\n";

try {
    // 1. Check jenis_tagihan categories
    echo "1. CHECKING JENIS TAGIHAN CATEGORIES:\n";
    echo "------------------------------------\n";
    
    $jenis_stats = DB::table('jenis_tagihans')
        ->select('kategori_tagihan', DB::raw('COUNT(*) as count'))
        ->groupBy('kategori_tagihan')
        ->get();
    
    foreach ($jenis_stats as $stat) {
        echo "  - {$stat->kategori_tagihan}: {$stat->count} jenis\n";
    }
    
    // 2. Check specific insidentil jenis tagihan
    echo "\n2. INSIDENTIL JENIS TAGIHAN DETAILS:\n";
    echo "-----------------------------------\n";
    
    $insidentil_jenis = DB::table('jenis_tagihans')
        ->where('kategori_tagihan', 'Insidentil')
        ->get();
    
    foreach ($insidentil_jenis as $jenis) {
        echo "  - ID: {$jenis->id} | Nama: {$jenis->nama} | Kategori: {$jenis->kategori_tagihan}\n";
    }
    
    // 3. Check tagihan_santris for insidentil
    echo "\n3. TAGIHAN SANTRIS FOR INSIDENTIL:\n";
    echo "----------------------------------\n";
    
    $activeTahunAjaran = TahunAjaran::getActive();
    echo "Active Tahun Ajaran ID: {$activeTahunAjaran->id}\n\n";
    
    $insidentil_tagihan = DB::table('tagihan_santris as ts')
        ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->where('ts.tahun_ajaran_id', $activeTahunAjaran->id)
        ->where('jt.kategori_tagihan', 'Insidentil')
        ->select('ts.*', 'jt.nama', 'jt.kategori_tagihan')
        ->get();
    
    echo "Total insidentil tagihan: " . count($insidentil_tagihan) . "\n";
    
    if (count($insidentil_tagihan) > 0) {
        echo "\nSample insidentil tagihan:\n";
        foreach (array_slice($insidentil_tagihan->toArray(), 0, 5) as $tagihan) {
            echo "  - Santri ID: {$tagihan->santri_id} | Jenis: {$tagihan->nama} | Bulan: {$tagihan->bulan} | Status: {$tagihan->status}\n";
        }
    } else {
        echo "\n❌ NO INSIDENTIL TAGIHAN FOUND!\n";
        
        // Check if there are any tagihan for any santri with insidentil jenis
        echo "\nChecking if insidentil jenis have any tagihan at all...\n";
        
        foreach ($insidentil_jenis as $jenis) {
            $count = DB::table('tagihan_santris')
                ->where('jenis_tagihan_id', $jenis->id)
                ->count();
            echo "  - {$jenis->nama} (ID: {$jenis->id}): {$count} tagihan records\n";
        }
    }
    
    // 4. Check specific santri
    echo "\n4. CHECK SPECIFIC SANTRI TAGIHAN:\n";
    echo "---------------------------------\n";
    
    $santri = DB::table('santris')->first();
    echo "Testing santri: {$santri->nama_santri} (ID: {$santri->id})\n";
    
    // All tagihan for this santri
    $all_tagihan = DB::table('tagihan_santris as ts')
        ->leftJoin('jenis_tagihans as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->where('ts.santri_id', $santri->id)
        ->where('ts.tahun_ajaran_id', $activeTahunAjaran->id)
        ->select('ts.*', 'jt.nama', 'jt.kategori_tagihan')
        ->get();
    
    $rutin_count = 0;
    $insidentil_count = 0;
    
    foreach ($all_tagihan as $tagihan) {
        if ($tagihan->kategori_tagihan == 'Rutin') $rutin_count++;
        elseif ($tagihan->kategori_tagihan == 'Insidentil') $insidentil_count++;
    }
    
    echo "  - Total tagihan: " . count($all_tagihan) . "\n";
    echo "  - Rutin: {$rutin_count}\n";
    echo "  - Insidentil: {$insidentil_count}\n";
    
    if ($insidentil_count > 0) {
        echo "\nInsidentil tagihan for this santri:\n";
        foreach ($all_tagihan as $tagihan) {
            if ($tagihan->kategori_tagihan == 'Insidentil') {
                echo "  - {$tagihan->nama} | Bulan: {$tagihan->bulan} | Nominal: " . number_format($tagihan->nominal_tagihan) . " | Status: {$tagihan->status}\n";
            }
        }
    }
    
    // 5. Test API endpoint simulation
    echo "\n5. SIMULATING API ENDPOINT:\n";
    echo "---------------------------\n";
    
    $tagihanSantri = TagihanSantri::where('santri_id', $santri->id)
        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
        ->with(['jenisTagihan', 'tahunAjaran', 'transaksis'])
        ->orderBy('jenis_tagihan_id')
        ->get();
    
    $payments = $tagihanSantri->map(function ($tagihan) {
        return [
            'id' => $tagihan->id,
            'jenis_tagihan' => $tagihan->jenisTagihan->nama,
            'jenis_tagihan_id' => $tagihan->jenis_tagihan_id,
            'bulan' => $tagihan->bulan,
            'nominal_tagihan' => $tagihan->nominal_tagihan,
            'nominal_dibayar' => $tagihan->nominal_dibayar,
            'status_pembayaran' => $tagihan->status,
            'kategori_tagihan' => $tagihan->jenisTagihan->kategori_tagihan ?? 'Rutin',
            'is_bulanan' => $tagihan->jenisTagihan->is_bulanan ?? false,
        ];
    });
    
    $insidentil_api = $payments->filter(function($payment) {
        return $payment['kategori_tagihan'] == 'Insidentil';
    });
    
    echo "API would return {$insidentil_api->count()} insidentil payments\n";
    
    if ($insidentil_api->count() > 0) {
        echo "Sample API insidentil data:\n";
        foreach ($insidentil_api->take(3) as $payment) {
            echo "  - {$payment['jenis_tagihan']} | {$payment['bulan']} | " . number_format($payment['nominal_tagihan']) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

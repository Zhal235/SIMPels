<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== CHECKING INSIDENTIL TAGIHAN STATUS ===\n\n";

// Check jenis_tagihan categories
echo "1. JENIS TAGIHAN CATEGORIES:\n";
$jenisTagihan = Capsule::table('jenis_tagihan')->select('id', 'nama', 'kategori', 'is_bulanan')->get();
foreach ($jenisTagihan as $jt) {
    echo "- ID: {$jt->id}, Nama: {$jt->nama}, Kategori: {$jt->kategori}, Is_Bulanan: {$jt->is_bulanan}\n";
}

echo "\n2. TAGIHAN SANTRI WITH INSIDENTAL CATEGORY:\n";
$tagihanInsidentil = Capsule::table('tagihan_santri as ts')
    ->join('jenis_tagihan as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
    ->join('santri as s', 'ts.santri_id', '=', 's.id')
    ->select('ts.id', 's.nama as santri_nama', 'jt.nama as jenis_nama', 'jt.kategori', 'ts.bulan', 'ts.jumlah', 'ts.status_pembayaran')
    ->where('jt.kategori', 'Insidental')
    ->get();

echo "Found " . count($tagihanInsidentil) . " insidental tagihan records:\n";
foreach ($tagihanInsidentil as $ti) {
    echo "- Santri: {$ti->santri_nama}, Jenis: {$ti->jenis_nama}, Bulan: {$ti->bulan}, Jumlah: {$ti->jumlah}, Status: {$ti->status_pembayaran}\n";
}

echo "\n3. SAMPLE SANTRI FOR TESTING:\n";
$santriSample = Capsule::table('santri')->select('id', 'nama')->limit(3)->get();
foreach ($santriSample as $s) {
    echo "- ID: {$s->id}, Nama: {$s->nama}\n";
    
    // Check tagihan for this santri
    $tagihanSantri = Capsule::table('tagihan_santri as ts')
        ->join('jenis_tagihan as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->select('jt.nama', 'jt.kategori', 'ts.bulan', 'ts.jumlah', 'ts.status_pembayaran')
        ->where('ts.santri_id', $s->id)
        ->orderBy('jt.kategori')
        ->orderBy('ts.bulan')
        ->get();
    
    foreach ($tagihanSantri as $ts) {
        echo "  * {$ts->kategori}: {$ts->nama} - {$ts->bulan} - Rp{$ts->jumlah} ({$ts->status_pembayaran})\n";
    }
    echo "\n";
}

echo "\n4. TAHUN AJARAN AKTIF:\n";
$tahunAjaran = Capsule::table('tahun_ajaran')
    ->select('id', 'nama', 'tahun_mulai', 'tahun_selesai', 'is_active')
    ->where('is_active', 1)
    ->first();

if ($tahunAjaran) {
    echo "Active: {$tahunAjaran->nama} (ID: {$tahunAjaran->id})\n";
} else {
    echo "No active tahun ajaran found!\n";
}

echo "\n5. API SIMULATION TEST:\n";
if ($santriSample->count() > 0) {
    $testSantriId = $santriSample->first()->id;
    echo "Testing API for Santri ID: {$testSantriId}\n";
    
    $apiResult = Capsule::table('tagihan_santri as ts')
        ->join('jenis_tagihan as jt', 'ts.jenis_tagihan_id', '=', 'jt.id')
        ->select([
            'ts.id',
            'jt.nama as jenis_tagihan',
            'jt.kategori as kategori_tagihan',
            'jt.is_bulanan',
            'ts.bulan',
            'ts.jumlah',
            'ts.status_pembayaran'
        ])
        ->where('ts.santri_id', $testSantriId)
        ->orderBy('ts.bulan')
        ->get();
    
    echo "API would return " . count($apiResult) . " records for this santri:\n";
    foreach ($apiResult as $record) {
        echo "- {$record->kategori_tagihan}: {$record->jenis_tagihan} ({$record->bulan}) - Rp{$record->jumlah}\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";

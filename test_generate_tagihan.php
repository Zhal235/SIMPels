<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JenisTagihan;
use App\Models\TagihanSantri;
use App\Models\Santri;
use App\Models\TahunAjaran;

echo "=== Test Generate Tagihan Santri Otomatis ===\n";

// Get active academic year
$tahunAjaran = TahunAjaran::where('is_active', true)->first();
echo "Tahun Ajaran Aktif: " . ($tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : 'Tidak ada') . "\n";

// Count existing bills before
$totalTagihanBefore = TagihanSantri::count();
echo "Total Tagihan Santri sebelum: $totalTagihanBefore\n";

// Count students
$totalSantri = Santri::where('status', 'aktif')->count();
echo "Total Santri Aktif: $totalSantri\n";

// Create a test bill type
echo "\n--- Membuat Jenis Tagihan Test ---\n";
$jenisTagihanTest = JenisTagihan::create([
    'nama' => 'Test Tagihan Otomatis - ' . date('Y-m-d H:i:s'),
    'deskripsi' => 'Test untuk memastikan generate tagihan santri otomatis',
    'kategori_tagihan' => 'Insidental',
    'is_bulanan' => false,
    'nominal' => 50000,
    'is_nominal_per_kelas' => false,
    'tahun_ajaran_id' => $tahunAjaran->id
]);

echo "Jenis Tagihan Test berhasil dibuat: ID = {$jenisTagihanTest->id}\n";

// Simulate the automatic generation that should happen in JenisTagihanController
echo "\n--- Menjalankan Generate Tagihan Santri ---\n";
try {
    $controller = new App\Http\Controllers\JenisTagihanController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('generateTagihanSantri');
    $method->setAccessible(true);
    
    $result = $method->invoke($controller, $jenisTagihanTest);
    echo "Generate tagihan santri berhasil: $result santri\n";
} catch (Exception $e) {
    echo "Error generate tagihan: " . $e->getMessage() . "\n";
}

// Count bills after
$totalTagihanAfter = TagihanSantri::count();
echo "Total Tagihan Santri sesudah: $totalTagihanAfter\n";
echo "Tagihan yang ditambahkan: " . ($totalTagihanAfter - $totalTagihanBefore) . "\n";

// Check generated bills for this test bill type
$generatedBills = TagihanSantri::where('jenis_tagihan_id', $jenisTagihanTest->id)->count();
echo "Tagihan yang dibuat untuk jenis test: $generatedBills\n";

// Cleanup - delete test data
echo "\n--- Membersihkan Data Test ---\n";
TagihanSantri::where('jenis_tagihan_id', $jenisTagihanTest->id)->delete();
$jenisTagihanTest->delete();
echo "Data test berhasil dihapus\n";

echo "\n=== Test Selesai ===\n";

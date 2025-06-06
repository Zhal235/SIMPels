<?php

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;

echo "=== Test Data untuk Form Keringanan ===\n";

// Cek tahun ajaran aktif
$tahunAjaran = TahunAjaran::getActive();
if ($tahunAjaran) {
    echo "Tahun Ajaran Aktif: {$tahunAjaran->nama_tahun_ajaran} (ID: {$tahunAjaran->id})\n";
} else {
    echo "TIDAK ADA TAHUN AJARAN AKTIF!\n";
}

// Cek beberapa santri aktif
$santris = Santri::where('status', 'aktif')->limit(3)->get(['id', 'nama_santri', 'nis']);
echo "\nBeberapa Santri Aktif:\n";
foreach ($santris as $santri) {
    echo "- ID: {$santri->id}, Nama: {$santri->nama_santri}, NIS: {$santri->nis}\n";
}

// Cek jenis tagihan
$jenisTagihan = JenisTagihan::limit(3)->get(['id', 'nama']);
echo "\nBeberapa Jenis Tagihan:\n";
foreach ($jenisTagihan as $jt) {
    echo "- ID: {$jt->id}, Nama: {$jt->nama}\n";
}

echo "\n=== Test Data Request ===\n";

// Simulasi data request yang valid
$testData = [
    'santri_id' => $santris->first()->id ?? 1,
    'jenis_keringanan' => 'potongan_nominal',
    'nilai_potongan' => 50000,
    'jenis_tagihan_id' => null,
    'keterangan' => 'Test keringanan',
    'santri_tertanggung_id' => null,
    'tanggal_mulai' => null,
    'tanggal_selesai' => null,
    'tahun_ajaran_id' => $tahunAjaran->id ?? 1
];

echo "Data test yang akan dikirim:\n";
foreach ($testData as $key => $value) {
    echo "- {$key}: " . ($value ?? 'null') . "\n";
}

echo "\n=== Test Validasi ===\n";

$rules = [
    'santri_id' => 'required|exists:santris,id',
    'jenis_keringanan' => 'required|in:potongan_persen,potongan_nominal,pembebasan,bayar_satu_gratis_satu',
    'nilai_potongan' => 'required_unless:jenis_keringanan,pembebasan,bayar_satu_gratis_satu|numeric|min:0',
    'jenis_tagihan_id' => 'nullable|exists:jenis_tagihans,id',
    'keterangan' => 'nullable|string|max:255',
    'santri_tertanggung_id' => 'required_if:jenis_keringanan,bayar_satu_gratis_satu|nullable|exists:santris,id',
    'tanggal_mulai' => 'nullable|date',
    'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai'
];

$validator = Validator::make($testData, $rules);

if ($validator->passes()) {
    echo "✅ Validasi BERHASIL\n";
} else {
    echo "❌ Validasi GAGAL:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- {$error}\n";
    }
}

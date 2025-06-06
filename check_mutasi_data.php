<?php

use App\Models\Santri;
use App\Models\KelasAnggota;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\MutasiSantri;

echo "=== ANALISIS SANTRI MUTASI & STATUS PEMBAYARAN ===\n\n";

// Ambil tahun ajaran aktif
$tahunAjaran = TahunAjaran::getActive();
if (!$tahunAjaran) {
    die("Tidak ada tahun ajaran aktif!\n");
}
echo "Tahun Ajaran Aktif: {$tahunAjaran->nama_tahun_ajaran}\n\n";

// 1. Santri dengan status mutasi
$santriMutasi = Santri::where('status', 'mutasi')->get();
echo "1. SANTRI DENGAN STATUS MUTASI: " . $santriMutasi->count() . " santri\n";
echo "----------------------------------------\n";
if ($santriMutasi->count() > 0) {
    foreach ($santriMutasi->take(5) as $index => $santri) {        // Cek data mutasi
        $dataMutasi = MutasiSantri::where('santri_id', $santri->id)->first();
        $tanggalMutasi = $dataMutasi && $dataMutasi->tanggal_mutasi ? 
            (is_string($dataMutasi->tanggal_mutasi) ? $dataMutasi->tanggal_mutasi : $dataMutasi->tanggal_mutasi->format('d-m-Y')) 
            : 'Tidak ada';
        $keteranganMutasi = $dataMutasi ? $dataMutasi->keterangan : 'Tidak ada';
        
        echo ($index + 1) . ". {$santri->nama_santri} (NIS: {$santri->nis})\n";
        echo "   - Tanggal Mutasi: {$tanggalMutasi}\n";
        echo "   - Keterangan: {$keteranganMutasi}\n";
        
        // Cek tagihan santri mutasi
        $tagihanSantri = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();
        
        echo "   - Jumlah Tagihan: {$tagihanSantri->count()}\n";
        
        // Status pembayaran
        $belumBayar = $tagihanSantri->where('status_pembayaran', 'belum_bayar')->count();
        $sebagian = $tagihanSantri->where('status_pembayaran', 'sebagian')->count();
        $lunas = $tagihanSantri->where('status_pembayaran', 'lunas')->count();
        
        echo "   - Status Pembayaran:\n";
        echo "     * Belum Bayar: {$belumBayar}\n";
        echo "     * Bayar Sebagian: {$sebagian}\n";
        echo "     * Lunas: {$lunas}\n";
        
        // Total tagihan yang belum dibayar
        $totalTagihan = $tagihanSantri->sum('nominal');
        $totalDibayar = $tagihanSantri->sum('nominal_dibayar');
        $totalSisa = $totalTagihan - $totalDibayar;
        
        echo "   - Total Tagihan: Rp " . number_format($totalTagihan, 0, ',', '.') . "\n";
        echo "   - Total Dibayar: Rp " . number_format($totalDibayar, 0, ',', '.') . "\n";
        echo "   - Total Sisa: Rp " . number_format($totalSisa, 0, ',', '.') . "\n";
        echo "\n";
    }
    
    if ($santriMutasi->count() > 5) {
        echo "   ...dan " . ($santriMutasi->count() - 5) . " santri lainnya\n";
    }
} else {
    echo "Tidak ada santri dengan status mutasi.\n";
}
echo "\n";

// 2. Santri yang pernah pindah kelas
$santriPindahKelas = Santri::where('status', 'aktif')
    ->whereHas('kelas_anggota', function($query) {
        $query->where('status', 'nonaktif');
    })
    ->get();
echo "2. SANTRI YANG PERNAH PINDAH KELAS: " . $santriPindahKelas->count() . " santri\n";
echo "----------------------------------------\n";
if ($santriPindahKelas->count() > 0) {
    foreach ($santriPindahKelas->take(5) as $index => $santri) {
        // Ambil kelas sekarang
        $kelasSekarang = $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Tidak ada kelas';
        
        // Ambil kelas terdahulu
        $kelasTerdahulu = $santri->kelas_anggota()
            ->where('status', 'nonaktif')
            ->with('kelas')
            ->orderBy('tanggal_selesai', 'desc')
            ->get()
            ->map(function($ka) { 
                return $ka->kelas ? $ka->kelas->nama . ' (' . $ka->tanggal_selesai->format('d-m-Y') . ')' : 'Unknown';
            })
            ->join(', ');
            
        echo ($index + 1) . ". {$santri->nama_santri} (NIS: {$santri->nis})\n";
        echo "   - Kelas Sekarang: {$kelasSekarang}\n";
        echo "   - Kelas Terdahulu: {$kelasTerdahulu}\n";
        
        // Cek tagihan santri
        $tagihanAll = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->with('jenis_tagihan')
            ->get();
            
        echo "   - Jumlah Tagihan: {$tagihanAll->count()}\n";
        
        // Lihat status pembayaran
        $belumBayar = $tagihanAll->where('status_pembayaran', 'belum_bayar')->count();
        $sebagian = $tagihanAll->where('status_pembayaran', 'sebagian')->count();
        $lunas = $tagihanAll->where('status_pembayaran', 'lunas')->count();
        
        echo "   - Status Pembayaran:\n";
        echo "     * Belum Bayar: {$belumBayar}\n";
        echo "     * Bayar Sebagian: {$sebagian}\n";
        echo "     * Lunas: {$lunas}\n";
        echo "\n";
    }
    
    if ($santriPindahKelas->count() > 5) {
        echo "   ...dan " . ($santriPindahKelas->count() - 5) . " santri lainnya\n";
    }
} else {
    echo "Tidak ada santri yang pernah pindah kelas.\n";
}

echo "\n";
echo "=== KESIMPULAN ===\n";

// Hitung rata-rata status pembayaran untuk santri mutasi
if ($santriMutasi->count() > 0) {
    $totalTagihanMutasi = 0;
    $totalDibayarMutasi = 0;
    
    foreach ($santriMutasi as $santri) {
        $tagihanSantri = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();
            
        $totalTagihanMutasi += $tagihanSantri->sum('nominal');
        $totalDibayarMutasi += $tagihanSantri->sum('nominal_dibayar');
    }
    
    $persentaseBayarMutasi = $totalTagihanMutasi > 0 ? ($totalDibayarMutasi / $totalTagihanMutasi) * 100 : 0;
    
    echo "Santri Mutasi:\n";
    echo "- Total Tagihan: Rp " . number_format($totalTagihanMutasi, 0, ',', '.') . "\n";
    echo "- Total Dibayar: Rp " . number_format($totalDibayarMutasi, 0, ',', '.') . "\n";
    echo "- Persentase Pembayaran: " . number_format($persentaseBayarMutasi, 2) . "%\n\n";
}

// Hitung rata-rata status pembayaran untuk santri pindah kelas
if ($santriPindahKelas->count() > 0) {
    $totalTagihanPindah = 0;
    $totalDibayarPindah = 0;
    
    foreach ($santriPindahKelas as $santri) {
        $tagihanSantri = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();
            
        $totalTagihanPindah += $tagihanSantri->sum('nominal');
        $totalDibayarPindah += $tagihanSantri->sum('nominal_dibayar');
    }
    
    $persentaseBayarPindah = $totalTagihanPindah > 0 ? ($totalDibayarPindah / $totalTagihanPindah) * 100 : 0;
    
    echo "Santri Pindah Kelas:\n";
    echo "- Total Tagihan: Rp " . number_format($totalTagihanPindah, 0, ',', '.') . "\n";
    echo "- Total Dibayar: Rp " . number_format($totalDibayarPindah, 0, ',', '.') . "\n";
    echo "- Persentase Pembayaran: " . number_format($persentaseBayarPindah, 2) . "%\n";
}

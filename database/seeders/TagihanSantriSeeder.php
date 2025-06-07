<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\TagihanSantri;
use Carbon\Carbon;

class TagihanSantriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tagihan_santris')->truncate();

        $start = new \DateTime('2023-07-01');
        $end   = new \DateTime('2025-06-01');
        $interval = new \DateInterval('P1M');

        $tahun = TahunAjaran::where('is_active', true)->first();
        if (!$tahun) {
            throw new \Exception('Tahun Ajaran tidak ditemukan. Pastikan TahunAjaranSeeder telah dijalankan.');
        }

        $santris = Santri::all();
        $jenisTagihan = JenisTagihan::all();
        
        // Filter jenis tagihan bulanan dan non-bulanan
        $jenisBulanan = $jenisTagihan->where('is_bulanan', true);
        $jenisNonBulanan = $jenisTagihan->where('is_bulanan', false);

        foreach ($santris as $santri) {
            $period = clone $start;
            
            // Special cases: Only generate tagihan after santri's entry date
            $tanggalMasuk = Carbon::parse($santri->tanggal_masuk);
            
            while ($period <= $end) {
                $bulan = $period->format('Y-m');
                $periodTime = Carbon::createFromFormat('Y-m-d', $period->format('Y-m-01'));
                
                // Skip if month is before santri's entry date
                if ($periodTime < $tanggalMasuk) {
                    $period->add($interval);
                    continue;
                }
                
                // Process monthly tagihan
                foreach ($jenisBulanan as $jenis) {
                    // find tarif per kelas
                    $tp = DB::table('jenis_tagihan_kelas')
                            ->where('jenis_tagihan_id', $jenis->id)
                            ->where('kelas_id', $santri->kelas_id)
                            ->first();
                    
                    $nominal = $tp ? $tp->nominal : 0;
                    
                    // Set jatuh tempo as 10th of current month
                    $jatuhTempo = Carbon::createFromFormat('Y-m-d', $period->format('Y-m-10'));
                    
                    TagihanSantri::create([
                        'santri_id'       => $santri->id,
                        'jenis_tagihan_id'=> $jenis->id,
                        'tahun_ajaran_id' => $tahun->id,
                        'bulan'           => $bulan,
                        'nominal_tagihan' => $nominal,
                        'nominal_dibayar' => 0,
                        'nominal_keringanan'=> 0,
                        'status'          => 'aktif',
                        'keterangan'      => null,
                        'tanggal_jatuh_tempo'=> $jatuhTempo,
                    ]);
                }
                
                // For non-bulanan tagihan, only create once at specific months
                if ($period->format('m') == '09' && $period->format('Y') == '2023') {
                    // Create non-bulanan tagihan for first semester
                    foreach ($jenisNonBulanan->where('nama', 'Ujian Semester 1') as $jenis) {
                        TagihanSantri::create([
                            'santri_id'       => $santri->id,
                            'jenis_tagihan_id'=> $jenis->id,
                            'tahun_ajaran_id' => $tahun->id,
                            'bulan'           => $bulan,
                            'nominal_tagihan' => rand(75000, 100000), // Random nominal for non-bulanan
                            'nominal_dibayar' => 0,
                            'nominal_keringanan'=> 0,
                            'status'          => 'aktif',
                            'keterangan'      => 'Ujian Semester Ganjil',
                            'tanggal_jatuh_tempo'=> Carbon::createFromFormat('Y-m-d', $period->format('Y-m-25')),
                        ]);
                    }
                }
                
                // For new students, add registration fees
                if ($period->format('Y-m') == substr($santri->tanggal_masuk, 0, 7)) {
                    foreach ($jenisNonBulanan->whereIn('nama', ['Administrasi Pendaftaran', 'Pembukaan Akun SIMPels', 'Mantassa & Khutbatul Arsy']) as $jenis) {
                        TagihanSantri::create([
                            'santri_id'       => $santri->id,
                            'jenis_tagihan_id'=> $jenis->id,
                            'tahun_ajaran_id' => $tahun->id,
                            'bulan'           => $bulan,
                            'nominal_tagihan' => rand(150000, 250000), // Random nominal for registration fees
                            'nominal_dibayar' => 0,
                            'nominal_keringanan'=> 0,
                            'status'          => 'aktif',
                            'keterangan'      => 'Biaya pendaftaran',
                            'tanggal_jatuh_tempo'=> Carbon::parse($santri->tanggal_masuk)->addDays(7),
                        ]);
                    }
                }
                
                $period->add($interval);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class TagihanSantriSeederFixed extends Seeder
{
    public function run(): void
    {
        // Clear existing data without truncate
        DB::table('pembayaran_santris')->delete();
        DB::table('tagihan_santris')->delete();

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
            
            while ($period <= $end) {
                $bulan = $period->format('Y-m');
                
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
                    
                    DB::table('tagihan_santris')->insert([
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
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
                
                // For non-bulanan tagihan, only create once at specific months
                if ($period->format('m') == '09' && $period->format('Y') == '2023') {
                    // Create non-bulanan tagihan for first semester
                    foreach ($jenisNonBulanan->where('nama', 'Ujian Semester 1') as $jenis) {
                        DB::table('tagihan_santris')->insert([
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
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
                
                $period->add($interval);
            }
        }
    }
}

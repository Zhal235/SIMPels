<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\TagihanSantri;
use Carbon\Carbon;

class TagihanSantriSeederWithoutTruncate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data using DELETE instead of TRUNCATE
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
        $bulanan = $jenisTagihan->where('is_bulanan', true);
        $nonBulanan = $jenisTagihan->where('is_bulanan', false);

        foreach ($santris as $santri) {
            // Generate tagihan bulanan
            foreach ($bulanan as $jenis) {
                $currentDate = clone $start;
                while ($currentDate <= $end) {                    TagihanSantri::create([
                        'santri_id' => $santri->id,
                        'jenis_tagihan_id' => $jenis->id,
                        'tahun_ajaran_id' => $tahun->id,
                        'nominal_tagihan' => $jenis->nominal ?? 0,
                        'nominal_dibayar' => 0,
                        'nominal_keringanan' => 0,
                        'tanggal_jatuh_tempo' => $currentDate->format('Y-m-d'),
                        'bulan' => $currentDate->format('Y-m'),
                        'status' => 'aktif',
                        'keterangan' => 'Tagihan ' . $jenis->nama . ' bulan ' . $currentDate->format('F Y')
                    ]);
                    $currentDate->add($interval);
                }
            }

            // Generate tagihan non-bulanan (sekali per tahun ajaran)
            foreach ($nonBulanan as $jenis) {                TagihanSantri::create([
                    'santri_id' => $santri->id,
                    'jenis_tagihan_id' => $jenis->id,
                    'tahun_ajaran_id' => $tahun->id,
                    'nominal_tagihan' => $jenis->nominal ?? 0,
                    'nominal_dibayar' => 0,
                    'nominal_keringanan' => 0,
                    'tanggal_jatuh_tempo' => $tahun->tanggal_mulai,
                    'bulan' => date('Y-m'),
                    'status' => 'aktif',
                    'keterangan' => 'Tagihan ' . $jenis->nama . ' tahun ajaran ' . $tahun->nama
                ]);
            }
        }
    }
}

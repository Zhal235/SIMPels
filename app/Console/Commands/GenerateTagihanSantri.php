<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;
use App\Models\JenisTagihanKelas;

class GenerateTagihanSantri extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:generate {--tahun-ajaran-id=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan santri berdasarkan jenis tagihan yang tersedia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tahunAjaranId = $this->option('tahun-ajaran-id');
        $force = $this->option('force');
        
        // Gunakan tahun ajaran aktif jika tidak dispesifikasi
        if (!$tahunAjaranId) {
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            if (!$activeTahunAjaran) {
                $this->error('Tidak ada tahun ajaran aktif. Silakan buat tahun ajaran terlebih dahulu.');
                return 1;
            }
            $tahunAjaranId = $activeTahunAjaran->id;
        } else {
            $activeTahunAjaran = TahunAjaran::find($tahunAjaranId);
            if (!$activeTahunAjaran) {
                $this->error('Tahun ajaran tidak ditemukan.');
                return 1;
            }
        }

        $this->info("Generating tagihan untuk tahun ajaran: {$activeTahunAjaran->nama_tahun_ajaran}");

        // Ambil semua santri aktif
        $santris = Santri::where('status', 'aktif')->with('kelasRelasi')->get();
        
        if ($santris->isEmpty()) {
            $this->error('Tidak ada santri aktif ditemukan.');
            return 1;
        }

        // Ambil semua jenis tagihan
        $jenisTagihans = JenisTagihan::all();
        
        if ($jenisTagihans->isEmpty()) {
            $this->error('Tidak ada jenis tagihan ditemukan.');
            return 1;
        }

        $this->info("Ditemukan {$santris->count()} santri dan {$jenisTagihans->count()} jenis tagihan");

        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($santris as $santri) {
            $this->info("Memproses santri: {$santri->nama_santri} (NIS: {$santri->nis})");
            
            // Ambil kelas santri
            $kelasNames = $santri->kelasRelasi->pluck('nama')->toArray();
            
            foreach ($jenisTagihans as $jenisTagihan) {
                // Tentukan nominal berdasarkan kelas jika ada
                $nominal = $jenisTagihan->nominal;
                  if ($jenisTagihan->is_nominal_per_kelas && !empty($kelasNames)) {
                    foreach ($kelasNames as $kelasName) {
                        $kelas = \App\Models\Kelas::where('nama', $kelasName)->first();
                        if ($kelas) {
                            $jenisTagihanKelas = JenisTagihanKelas::where('jenis_tagihan_id', $jenisTagihan->id)
                                ->where('kelas_id', $kelas->id)
                                ->first();
                            
                            if ($jenisTagihanKelas) {
                                $nominal = $jenisTagihanKelas->nominal;
                                break; // Gunakan nominal dari kelas pertama yang ditemukan
                            }
                        }
                    }
                }

                if ($jenisTagihan->kategori_tagihan === 'Rutin' && $jenisTagihan->is_bulanan) {
                    // Generate tagihan rutin bulanan
                    $bulanList = $this->generateBulanList($activeTahunAjaran);
                    
                    foreach ($bulanList as $bulan) {
                        $exists = TagihanSantri::where('santri_id', $santri->id)
                            ->where('jenis_tagihan_id', $jenisTagihan->id)
                            ->where('tahun_ajaran_id', $tahunAjaranId)
                            ->where('bulan', $bulan)
                            ->exists();

                        if (!$exists || $force) {
                            if ($exists && $force) {
                                TagihanSantri::where('santri_id', $santri->id)
                                    ->where('jenis_tagihan_id', $jenisTagihan->id)
                                    ->where('tahun_ajaran_id', $tahunAjaranId)
                                    ->where('bulan', $bulan)
                                    ->delete();
                            }

                            // Untuk tagihan rutin, jatuh tempo selalu tanggal 10 setiap bulan
                            $tahunBulan = explode('-', $bulan);
                            $tahun = $tahunBulan[0] ?? date('Y');
                            $bulanAngka = $tahunBulan[1] ?? date('m');
                            
                            // Pastikan tanggalnya valid (jika bulan hanya punya 28-30 hari)
                            $tanggal = 10;
                            $lastDayOfMonth = cal_days_in_month(CAL_GREGORIAN, (int)$bulanAngka, (int)$tahun);
                            if ($tanggal > $lastDayOfMonth) {
                                $tanggal = $lastDayOfMonth;
                            }
                            
                            $tanggalJatuhTempo = "{$tahun}-{$bulanAngka}-{$tanggal}"; // Format: YYYY-MM-DD (tanggal 10)
                            
                            TagihanSantri::create([
                                'santri_id' => $santri->id,
                                'jenis_tagihan_id' => $jenisTagihan->id,
                                'tahun_ajaran_id' => $tahunAjaranId,
                                'bulan' => $bulan,
                                'nominal_tagihan' => $nominal,
                                'nominal_dibayar' => 0,
                                'status' => 'aktif',
                                'tanggal_jatuh_tempo' => $tanggalJatuhTempo
                            ]);
                            
                            $totalCreated++;
                            $this->line("  ✓ {$jenisTagihan->nama} - {$bulan}: Rp " . number_format($nominal));
                        } else {
                            $totalSkipped++;
                        }
                    }
                } else {
                    // Generate tagihan insidentil atau rutin tahunan
                    $exists = TagihanSantri::where('santri_id', $santri->id)
                        ->where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('tahun_ajaran_id', $tahunAjaranId)
                        ->exists();

                    if (!$exists || $force) {
                        if ($exists && $force) {
                            TagihanSantri::where('santri_id', $santri->id)
                                ->where('jenis_tagihan_id', $jenisTagihan->id)
                                ->where('tahun_ajaran_id', $tahunAjaranId)
                                ->delete();
                        }                        // Untuk tagihan insidental, gunakan setting tanggal jatuh tempo dari jenis tagihan
                        $bulanTagihan = $activeTahunAjaran->tahun_mulai . '-07'; // Format bulan yang konsisten (Juli tahun mulai)
                        $tahunBulan = explode('-', $bulanTagihan);
                        $tahun = $tahunBulan[0] ?? date('Y');
                        $bulanAngka = $tahunBulan[1] ?? date('m');
                        
                        // Tentukan bulan jatuh tempo (tambahkan bulan sesuai setting)
                        $bulanJatuhTempo = $bulanAngka;
                        if (($jenisTagihan->bulan_jatuh_tempo ?? 0) > 0) {
                            // Tambahkan bulan sesuai setting
                            $date = \Carbon\Carbon::createFromDate($tahun, $bulanAngka, 1);
                            $date->addMonths((int)$jenisTagihan->bulan_jatuh_tempo);
                            $tahun = $date->format('Y');
                            $bulanJatuhTempo = $date->format('m');
                        }
                        
                        // Gunakan tanggal jatuh tempo dari setting (default: 10)
                        $tanggal = $jenisTagihan->tanggal_jatuh_tempo ?? 10;
                        
                        // Pastikan tanggalnya valid (jika bulan hanya punya 28-30 hari)
                        $lastDayOfMonth = cal_days_in_month(CAL_GREGORIAN, (int)$bulanJatuhTempo, (int)$tahun);
                        if ($tanggal > $lastDayOfMonth) {
                            $tanggal = $lastDayOfMonth;
                        }
                        
                        $tanggalJatuhTempo = "{$tahun}-{$bulanJatuhTempo}-{$tanggal}";
                        
                        TagihanSantri::create([
                            'santri_id' => $santri->id,
                            'jenis_tagihan_id' => $jenisTagihan->id,
                            'tahun_ajaran_id' => $tahunAjaranId,
                            'bulan' => $bulanTagihan,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 0,
                            'status' => 'aktif',
                            'tanggal_jatuh_tempo' => $tanggalJatuhTempo
                        ]);
                        
                        $totalCreated++;
                        $this->line("  ✓ {$jenisTagihan->nama}: Rp " . number_format($nominal));
                    } else {
                        $totalSkipped++;
                    }
                }
            }
        }

        $this->info("Generate tagihan selesai!");
        $this->info("Total tagihan dibuat: {$totalCreated}");
        $this->info("Total tagihan dilewati (sudah ada): {$totalSkipped}");
        
        return 0;
    }    /**
     * Generate daftar bulan untuk tagihan rutin
     */
    private function generateBulanList($tahunAjaran)
    {
        $bulanList = [];
        $tahunMulai = (int) $tahunAjaran->tahun_mulai;
        $tahunAkhir = (int) $tahunAjaran->tahun_selesai;
        
        // Juli - Desember (tahun mulai)
        for ($i = 7; $i <= 12; $i++) {
            $bulanList[] = $tahunMulai . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        // Januari - Juni (tahun akhir)
        for ($i = 1; $i <= 6; $i++) {
            $bulanList[] = $tahunAkhir . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        return $bulanList;
    }
}

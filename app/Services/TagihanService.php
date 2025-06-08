<?php

namespace App\Services;

use App\Models\TagihanSantri;
use App\Models\TahunAjaran;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\JenisTagihanKelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanService
{
    /**
     * Copy routine tagihan from previous academic year to new academic year
     * This ensures routine tagihan continue for active santri without manual re-entry
     */
    public function copyRoutineTagihanToNewYear(TahunAjaran $newTahunAjaran, ?TahunAjaran $sourceTahunAjaran = null): array
    {
        try {
            DB::beginTransaction();
            
            // If no source year specified, use the previous active year
            if (!$sourceTahunAjaran) {
                $sourceTahunAjaran = TahunAjaran::where('tahun_mulai', '<', $newTahunAjaran->tahun_mulai)
                    ->orderBy('tahun_mulai', 'desc')
                    ->first();
                    
                if (!$sourceTahunAjaran) {
                    return [
                        'success' => false,
                        'message' => 'Tidak ada tahun ajaran sebelumnya untuk disalin.',
                        'copied_count' => 0
                    ];
                }
            }
            
            // Get all active santri
            $activeSantris = Santri::where('status', 'aktif')->get();
            
            if ($activeSantris->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada santri aktif ditemukan.',
                    'copied_count' => 0
                ];
            }
            
            // Get routine jenis tagihan that should continue across years
            $routineJenisTagihan = JenisTagihan::where('kategori_tagihan', 'Rutin')->get();
            
            if ($routineJenisTagihan->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada jenis tagihan rutin ditemukan.',
                    'copied_count' => 0
                ];
            }
            
            $copiedCount = 0;
            $skippedCount = 0;
            
            foreach ($activeSantris as $santri) {
                // Check if santri had routine tagihan in previous year
                $hadPreviousTagihan = TagihanSantri::where('santri_id', $santri->id)
                    ->where('tahun_ajaran_id', $sourceTahunAjaran->id)
                    ->whereHas('jenisTagihan', function($q) {
                        $q->where('kategori_tagihan', 'Rutin');
                    })
                    ->exists();
                
                if (!$hadPreviousTagihan) {
                    continue; // Skip santri who didn't have routine tagihan in previous year
                }
                
                foreach ($routineJenisTagihan as $jenisTagihan) {
                    // Check if tagihan already exists for this santri in new year
                    $existingTagihan = TagihanSantri::where('santri_id', $santri->id)
                        ->where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('tahun_ajaran_id', $newTahunAjaran->id)
                        ->exists();
                    
                    if ($existingTagihan) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // Get the last nominal amount for this santri and jenis tagihan
                    $lastTagihan = TagihanSantri::where('santri_id', $santri->id)
                        ->where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('tahun_ajaran_id', $sourceTahunAjaran->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    $nominal = $jenisTagihan->nominal; // Default nominal
                    
                    // Use nominal from previous year if available (in case there were adjustments)
                    if ($lastTagihan) {
                        $nominal = $lastTagihan->nominal_tagihan;
                    }
                    
                    // Check if there's a specific nominal for santri's class
                    $kelasNames = $santri->kelasRelasi->pluck('nama')->toArray();
                    if ($jenisTagihan->is_nominal_per_kelas && !empty($kelasNames)) {
                        foreach ($kelasNames as $kelasName) {
                            $kelas = \App\Models\Kelas::where('nama', $kelasName)->first();
                            if ($kelas) {
                                $jenisTagihanKelas = JenisTagihanKelas::where('jenis_tagihan_id', $jenisTagihan->id)
                                    ->where('kelas_id', $kelas->id)
                                    ->first();
                                
                                if ($jenisTagihanKelas) {
                                    $nominal = $jenisTagihanKelas->nominal;
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Generate tagihan for new academic year
                    if ($jenisTagihan->is_bulanan) {
                        // Generate monthly routine tagihan
                        $bulanList = $this->generateBulanList($newTahunAjaran);
                        
                        foreach ($bulanList as $bulan) {
                            TagihanSantri::create([
                                'santri_id' => $santri->id,
                                'jenis_tagihan_id' => $jenisTagihan->id,
                                'tahun_ajaran_id' => $newTahunAjaran->id,
                                'bulan' => $bulan,
                                'nominal_tagihan' => $nominal,
                                'nominal_dibayar' => 0,
                                'nominal_keringanan' => 0,
                                'status' => 'aktif',
                                'tanggal_tagihan' => now(),
                                'bulan_tahun' => Carbon::createFromFormat('Y-m', $bulan)->format('F Y')
                            ]);
                            $copiedCount++;
                        }
                    } else {
                        // Generate annual routine tagihan
                        TagihanSantri::create([
                            'santri_id' => $santri->id,
                            'jenis_tagihan_id' => $jenisTagihan->id,
                            'tahun_ajaran_id' => $newTahunAjaran->id,
                            'bulan' => $newTahunAjaran->tahun_mulai . '-07', // Start of academic year
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 0,
                            'nominal_keringanan' => 0,
                            'status' => 'aktif',
                            'tanggal_tagihan' => now(),
                            'bulan_tahun' => 'Tahunan ' . $newTahunAjaran->nama
                        ]);
                        $copiedCount++;
                    }
                }
            }
            
            DB::commit();
            
            Log::info("TagihanService: Copied {$copiedCount} routine tagihan from {$sourceTahunAjaran->nama} to {$newTahunAjaran->nama}");
            
            return [
                'success' => true,
                'message' => "Berhasil menyalin {$copiedCount} tagihan rutin dari tahun ajaran {$sourceTahunAjaran->nama} ke {$newTahunAjaran->nama}. {$skippedCount} tagihan dilewati karena sudah ada.",
                'copied_count' => $copiedCount,
                'skipped_count' => $skippedCount,
                'source_year' => $sourceTahunAjaran->nama,
                'target_year' => $newTahunAjaran->nama
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("TagihanService: Error copying routine tagihan - " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyalin tagihan rutin: ' . $e->getMessage(),
                'copied_count' => 0
            ];
        }
    }
    
    /**
     * Generate monthly tagihan for a specific santri and academic year
     */
    public function generateTagihanForSantri(Santri $santri, TahunAjaran $tahunAjaran, ?array $jenisTagihanIds = null): array
    {
        try {
            DB::beginTransaction();
            
            $jenisTagihans = JenisTagihan::when($jenisTagihanIds, function($q) use ($jenisTagihanIds) {
                return $q->whereIn('id', $jenisTagihanIds);
            })->get();
            
            $createdCount = 0;
            
            foreach ($jenisTagihans as $jenisTagihan) {
                // Check if already exists
                $exists = TagihanSantri::where('santri_id', $santri->id)
                    ->where('jenis_tagihan_id', $jenisTagihan->id)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->exists();
                
                if ($exists) {
                    continue;
                }
                
                // Determine nominal amount
                $nominal = $this->determineNominalForSantri($santri, $jenisTagihan);
                
                if ($jenisTagihan->is_bulanan && $jenisTagihan->kategori_tagihan === 'Rutin') {
                    // Generate monthly tagihan
                    $bulanList = $this->generateBulanList($tahunAjaran);
                    
                    foreach ($bulanList as $bulan) {
                        TagihanSantri::create([
                            'santri_id' => $santri->id,
                            'jenis_tagihan_id' => $jenisTagihan->id,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'bulan' => $bulan,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 0,
                            'nominal_keringanan' => 0,
                            'status' => 'aktif',
                            'tanggal_tagihan' => now(),
                            'bulan_tahun' => Carbon::createFromFormat('Y-m', $bulan)->format('F Y')
                        ]);
                        $createdCount++;
                    }
                } else {
                    // Generate single tagihan
                    TagihanSantri::create([
                        'santri_id' => $santri->id,
                        'jenis_tagihan_id' => $jenisTagihan->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'bulan' => $tahunAjaran->tahun_mulai . '-07',
                        'nominal_tagihan' => $nominal,
                        'nominal_dibayar' => 0,
                        'nominal_keringanan' => 0,
                        'status' => 'aktif',
                        'tanggal_tagihan' => now(),
                        'bulan_tahun' => $jenisTagihan->is_bulanan ? 'Tahunan ' . $tahunAjaran->nama : 'Insidentil'
                    ]);
                    $createdCount++;
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Berhasil membuat {$createdCount} tagihan untuk santri {$santri->nama_santri}.",
                'created_count' => $createdCount
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat tagihan: ' . $e->getMessage(),
                'created_count' => 0
            ];
        }
    }
    
    /**
     * Determine nominal amount for santri based on class if applicable
     */
    private function determineNominalForSantri(Santri $santri, JenisTagihan $jenisTagihan): int
    {
        $nominal = $jenisTagihan->nominal;
        
        if ($jenisTagihan->is_nominal_per_kelas) {
            $kelasNames = $santri->kelasRelasi->pluck('nama')->toArray();
            
            foreach ($kelasNames as $kelasName) {
                $kelas = \App\Models\Kelas::where('nama', $kelasName)->first();
                if ($kelas) {
                    $jenisTagihanKelas = JenisTagihanKelas::where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('kelas_id', $kelas->id)
                        ->first();
                    
                    if ($jenisTagihanKelas) {
                        $nominal = $jenisTagihanKelas->nominal;
                        break;
                    }
                }
            }
        }
        
        return $nominal;
    }
    
    /**
     * Generate month list for academic year (July to June)
     */
    private function generateBulanList(TahunAjaran $tahunAjaran): array
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
    
    /**
     * Check if santri has outstanding tagihan from previous years
     */
    public function hasOutstandingTagihanFromPreviousYears(Santri $santri, TahunAjaran $currentTahunAjaran): bool
    {
        return TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', '!=', $currentTahunAjaran->id)
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->exists();
    }
    
    /**
     * Get outstanding tagihan summary for santri
     */
    public function getOutstandingTagihanSummary(Santri $santri, TahunAjaran $currentTahunAjaran): array
    {
        // Current year outstanding
        $currentYearTagihan = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $currentTahunAjaran->id)
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get();
        
        // Previous years outstanding
        $previousYearsTagihan = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', '!=', $currentTahunAjaran->id)
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get();
        
        return [
            'current_year_total' => $currentYearTagihan->sum('sisa_tagihan'),
            'current_year_count' => $currentYearTagihan->count(),
            'previous_years_total' => $previousYearsTagihan->sum('sisa_tagihan'),
            'previous_years_count' => $previousYearsTagihan->count(),
            'grand_total' => $currentYearTagihan->sum('sisa_tagihan') + $previousYearsTagihan->sum('sisa_tagihan')
        ];
    }

    /**
     * Copy tagihan between years with specific categories
     */
    public function copyTagihanBetweenYears($sourceYearId, $targetYearId, $categories = ['Rutin'], $replaceExisting = false): array
    {
        try {
            DB::beginTransaction();
            
            $sourceYear = TahunAjaran::find($sourceYearId);
            $targetYear = TahunAjaran::find($targetYearId);
            
            if (!$sourceYear || !$targetYear) {
                return [
                    'success' => false,
                    'message' => 'Tahun ajaran tidak ditemukan.'
                ];
            }
            
            // Get active santri
            $activeSantris = Santri::where('status', 'aktif')->get();
            
            if ($activeSantris->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada santri aktif ditemukan.'
                ];
            }
            
            // Get jenis tagihan with specified categories
            $jenisTagihans = JenisTagihan::whereIn('kategori_tagihan', $categories)->get();
            
            if ($jenisTagihans->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada jenis tagihan dengan kategori yang dipilih.'
                ];
            }
            
            $createdCount = 0;
            $updatedCount = 0;
            
            foreach ($activeSantris as $santri) {
                foreach ($jenisTagihans as $jenisTagihan) {
                    // Check if santri had this tagihan in source year
                    $sourceTagihan = TagihanSantri::where('santri_id', $santri->id)
                        ->where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('tahun_ajaran_id', $sourceYear->id)
                        ->first();
                    
                    if (!$sourceTagihan) {
                        continue; // Skip if santri didn't have this tagihan in source year
                    }
                    
                    // Check if tagihan already exists in target year
                    $existingTagihan = TagihanSantri::where('santri_id', $santri->id)
                        ->where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('tahun_ajaran_id', $targetYear->id)
                        ->first();
                    
                    if ($existingTagihan) {
                        if ($replaceExisting) {
                            // For monthly bills, we should delete existing and create new ones for all months
                            if ($jenisTagihan->is_bulanan) {
                                // Delete existing monthly tagihan for this santri, jenis, and year
                                TagihanSantri::where('santri_id', $santri->id)
                                    ->where('jenis_tagihan_id', $jenisTagihan->id)
                                    ->where('tahun_ajaran_id', $targetYear->id)
                                    ->delete();
                                
                                // Create new tagihan for each month
                                $bulanList = $this->generateBulanList($targetYear);
                                foreach ($bulanList as $bulan) {
                                    TagihanSantri::create([
                                        'santri_id' => $santri->id,
                                        'jenis_tagihan_id' => $jenisTagihan->id,
                                        'tahun_ajaran_id' => $targetYear->id,
                                        'bulan' => $bulan, // Use generated month from target year
                                        'nominal_tagihan' => $sourceTagihan->nominal_tagihan,
                                        'nominal_dibayar' => 0,
                                        'nominal_keringanan' => 0,
                                        'tanggal_jatuh_tempo' => $this->calculateDueDateForBulanan($bulan),
                                        'status' => 'aktif',
                                        'keterangan' => 'Disalin dari ' . $sourceYear->nama_tahun_ajaran
                                    ]);
                                }
                                $updatedCount++;
                            } else {
                                // For non-monthly bills, just update the existing record
                                $existingTagihan->update([
                                    'nominal_tagihan' => $sourceTagihan->nominal_tagihan,
                                    'bulan' => $targetYear->tahun_mulai . '-07', // Default to July of start year for annual bills
                                    'tanggal_jatuh_tempo' => $this->calculateDueDate($jenisTagihan, $targetYear),
                                    'status' => 'aktif',
                                    'keterangan' => 'Disalin dari ' . $sourceYear->nama_tahun_ajaran
                                ]);
                                $updatedCount++;
                            }
                        }
                        continue; // Skip if exists and not replacing
                    }
                    
                    // For monthly bills, we need to generate all months in the academic year
                    if ($jenisTagihan->is_bulanan) {
                        $bulanList = $this->generateBulanList($targetYear);
                        
                        foreach ($bulanList as $bulan) {
                            $newTagihan = TagihanSantri::create([
                                'santri_id' => $santri->id,
                                'jenis_tagihan_id' => $jenisTagihan->id,
                                'tahun_ajaran_id' => $targetYear->id,
                                'bulan' => $bulan, // Use generated month from target year
                                'nominal_tagihan' => $sourceTagihan->nominal_tagihan,
                                'nominal_dibayar' => 0,
                                'nominal_keringanan' => 0,
                                'tanggal_jatuh_tempo' => $this->calculateDueDateForBulanan($bulan),
                                'status' => 'aktif',
                                'keterangan' => 'Disalin dari ' . $sourceYear->nama_tahun_ajaran
                            ]);
                            $createdCount++;
                        }
                    } else {
                        // For non-monthly bills, create a single record
                        $newTagihan = TagihanSantri::create([
                            'santri_id' => $santri->id,
                            'jenis_tagihan_id' => $jenisTagihan->id,
                            'tahun_ajaran_id' => $targetYear->id,
                            'bulan' => $targetYear->tahun_mulai . '-07', // Default to July of start year for annual bills
                            'nominal_tagihan' => $sourceTagihan->nominal_tagihan,
                            'nominal_dibayar' => 0,
                            'nominal_keringanan' => 0,
                            'tanggal_jatuh_tempo' => $this->calculateDueDate($jenisTagihan, $targetYear),
                            'status' => 'aktif',
                            'keterangan' => 'Disalin dari ' . $sourceYear->nama_tahun_ajaran
                        ]);
                        $createdCount++;
                    }
                    
                    if ($newTagihan) {
                        $createdCount++;
                    }
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Berhasil menyalin tagihan. $createdCount tagihan baru dibuat" . ($updatedCount > 0 ? ", $updatedCount tagihan diperbarui." : "."),
                'created_count' => $createdCount,
                'updated_count' => $updatedCount
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error copying tagihan between years: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyalin tagihan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate due date based on jenis tagihan and tahun ajaran
     */
    private function calculateDueDate(JenisTagihan $jenisTagihan, TahunAjaran $tahunAjaran): Carbon
    {
        if ($jenisTagihan->is_bulanan) {
            // For monthly payments, set to 10th of current month
            return Carbon::now()->day(10);
        } else {
            // For annual payments, set to 3 months after year start
            return Carbon::createFromDate($tahunAjaran->tahun_mulai, 1, 1)->addMonths(3);
        }
    }

    /**
     * Calculate due date for specific month
     */
    private function calculateDueDateForBulanan($bulan)
    {
        // Format bulan: YYYY-MM
        $parts = explode('-', $bulan);
        if (count($parts) === 2) {
            $tahun = (int) $parts[0];
            $bulanInt = (int) $parts[1];
            
            // Set jatuh tempo pada tanggal 10 setiap bulan
            return Carbon::createFromDate($tahun, $bulanInt, 10);
        }
        
        // Fallback: 30 hari dari sekarang
        return Carbon::now()->addDays(30);
    }
}

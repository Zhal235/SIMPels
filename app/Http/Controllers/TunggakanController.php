<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanSantri;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TunggakanExport;
use App\Services\TagihanService;

class TunggakanController extends Controller
{
    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    /**
     * Menampilkan daftar tunggakan santri aktif
     */
    public function santriAktif(Request $request)
    {
        $tahunAjaran = TahunAjaran::getActive();

        if (!$tahunAjaran) {
            return redirect()->route('keuangan.dashboard')->with('error', 'Tidak ada tahun ajaran aktif');
        }        // Query tunggakan santri aktif
        $tunggakan = TagihanSantri::with(['santri', 'jenisTagihan'])
            ->whereHas('santri', function($q) {
                $q->where('status', 'aktif');
            })
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get()
            ->groupBy('santri_id');

        // Mendapatkan data untuk filter
        $jenisTagihan = JenisTagihan::all();
        
        return view('keuangan.tunggakan.santri_aktif', compact(
            'tunggakan', 
            'tahunAjaran',
            'jenisTagihan'
        ));
    }

    /**
     * Menampilkan daftar tunggakan santri mutasi
     */
    public function santriMutasi(Request $request)
    {
        $tahunAjaran = TahunAjaran::getActive();

        if (!$tahunAjaran) {
            return redirect()->route('keuangan.dashboard')->with('error', 'Tidak ada tahun ajaran aktif');
        }        // Query tunggakan santri mutasi
        $tunggakan = TagihanSantri::with(['santri', 'jenisTagihan'])
            ->whereHas('santri', function($q) {
                $q->where('status', 'mutasi');
            })
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get()
            ->groupBy('santri_id');

        // Mendapatkan data untuk filter
        $jenisTagihan = JenisTagihan::all();
        
        return view('keuangan.tunggakan.santri_mutasi', compact(
            'tunggakan', 
            'tahunAjaran',
            'jenisTagihan'
        ));
    }

    /**
     * Menampilkan daftar tunggakan santri alumni
     */
    public function santriAlumni(Request $request)
    {
        $tahunAjaran = TahunAjaran::getActive();

        if (!$tahunAjaran) {
            return redirect()->route('keuangan.dashboard')->with('error', 'Tidak ada tahun ajaran aktif');
        }        // Query tunggakan santri alumni
        $tunggakan = TagihanSantri::with(['santri', 'jenisTagihan'])
            ->whereHas('santri', function($q) {
                $q->where('status', 'alumni');
            })
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get()
            ->groupBy('santri_id');

        // Mendapatkan data untuk filter
        $jenisTagihan = JenisTagihan::all();
        
        return view('keuangan.tunggakan.santri_alumni', compact(
            'tunggakan', 
            'tahunAjaran',
            'jenisTagihan'
        ));
    }

    /**
     * Menampilkan detail tunggakan satu santri
     */
    public function detail($santri_id)
    {
        $santri = Santri::with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])->findOrFail($santri_id);
        $tahunAjaran = TahunAjaran::getActive();
        
        if (!$tahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }
        
        // Mendapatkan semua tahun ajaran
        $allTahunAjaran = TahunAjaran::orderBy('tahun_mulai', 'desc')->get();
        
        // Mendapatkan semua tagihan yang belum lunas untuk tahun ajaran aktif
        $tagihanTahunIni = TagihanSantri::with(['jenisTagihan', 'tahunAjaran'])
            ->where('santri_id', $santri_id)
            ->where('status', 'aktif')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->orderBy('tanggal_jatuh_tempo')
            ->get();
        
        // Mendapatkan semua tagihan yang belum lunas untuk tahun ajaran sebelumnya
        $tagihanTahunSebelumnya = TagihanSantri::with(['jenisTagihan', 'tahunAjaran'])
            ->where('santri_id', $santri_id)
            ->where('status', 'aktif')
            ->where('tahun_ajaran_id', '!=', $tahunAjaran->id)
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->orderBy('tanggal_jatuh_tempo')
            ->get();
        
        // Pisahkan tagihan yang sudah jatuh tempo dan belum jatuh tempo (tahun ini)
        $tagihanJatuhTempoTahunIni = $tagihanTahunIni->filter(function($tagihan) {
            return $tagihan->is_jatuh_tempo;
        });
        
        $tagihanBelumJatuhTempoTahunIni = $tagihanTahunIni->filter(function($tagihan) {
            return !$tagihan->is_jatuh_tempo;
        });
        
        // Pisahkan tagihan yang sudah jatuh tempo dan belum jatuh tempo (tahun sebelumnya)
        $tagihanJatuhTempoTahunSebelumnya = $tagihanTahunSebelumnya->filter(function($tagihan) {
            return $tagihan->is_jatuh_tempo;
        });
        
        $tagihanBelumJatuhTempoTahunSebelumnya = $tagihanTahunSebelumnya->filter(function($tagihan) {
            return !$tagihan->is_jatuh_tempo;
        });
        
        // Menghitung total tunggakan
        $totalTunggakanTahunIni = $tagihanTahunIni->sum('sisa_tagihan');
        $totalTunggakanTahunSebelumnya = $tagihanTahunSebelumnya->sum('sisa_tagihan');
        $totalTunggakan = $totalTunggakanTahunIni + $totalTunggakanTahunSebelumnya;
        
        // Menghitung total tunggakan jatuh tempo
        $totalTunggakanJatuhTempo = $tagihanJatuhTempoTahunIni->sum('sisa_tagihan') + 
                                   $tagihanJatuhTempoTahunSebelumnya->sum('sisa_tagihan');
        
        return view('keuangan.tunggakan.detail', compact(
            'santri', 
            'tagihanTahunIni',
            'tagihanTahunSebelumnya', 
            'tagihanJatuhTempoTahunIni',
            'tagihanBelumJatuhTempoTahunIni',
            'tagihanJatuhTempoTahunSebelumnya',
            'tagihanBelumJatuhTempoTahunSebelumnya',
            'totalTunggakanTahunIni',
            'totalTunggakanTahunSebelumnya',
            'totalTunggakan',
            'totalTunggakanJatuhTempo',
            'tahunAjaran',
            'allTahunAjaran'
        ));
    }    /**
     * Export data tunggakan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $tahunAjaran = TahunAjaran::getActive();
        if (!$tahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }

        // Jika ada parameter santri_id, export hanya untuk 1 santri
        if ($request->has('santri_id')) {
            $santri = Santri::find($request->santri_id);
            if (!$santri) {
                return redirect()->back()->with('error', 'Santri tidak ditemukan');
            }

            $filename = 'tunggakan_' . str_replace(' ', '_', strtolower($santri->nama_santri)) . '_' . date('dmY') . '.xlsx';
              $tagihan = TagihanSantri::with(['jenisTagihan', 'santri'])
                ->where('santri_id', $santri->id)
                ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
                ->get();
                
            // Create Excel Export
            return $this->generateExcel($tagihan, $filename, 'Tunggakan ' . $santri->nama_santri);
        }
        
        // Export berdasarkan tipe
        $tipe = $request->get('tipe', 'aktif');
          if ($tipe == 'mutasi') {
            $query = TagihanSantri::with(['santri', 'jenisTagihan'])
                ->whereHas('santri', function($q) {
                    $q->where('status', 'mutasi');
                })
                ->where('status', 'aktif')
                ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan');
            $filename = 'tunggakan_santri_mutasi_' . date('dmY') . '.xlsx';
            $title = 'Daftar Tunggakan Santri Mutasi';        } elseif ($tipe == 'alumni') {
            $query = TagihanSantri::with(['santri', 'jenisTagihan'])
                ->whereHas('santri', function($q) {
                    $q->where('status', 'alumni');
                })
                ->where('status', 'aktif')
                ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan');
            $filename = 'tunggakan_santri_alumni_' . date('dmY') . '.xlsx';
            $title = 'Daftar Tunggakan Santri Alumni';        } else {
            $query = TagihanSantri::with(['santri', 'jenisTagihan'])
                ->whereHas('santri', function($q) {
                    $q->where('status', 'aktif');
                })
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('status', 'aktif')
                ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan');
            $filename = 'tunggakan_santri_aktif_' . date('dmY') . '.xlsx';
            $title = 'Daftar Tunggakan Santri Aktif';
        }
          $tagihan = $query->get();
            
        // Create Excel Export
        return $this->generateExcel($tagihan, $filename, $title);
    }    /**
     * Generate Excel export
     */
    private function generateExcel($tagihan, $filename, $title)
    {
        // Generate Excel using Laravel Excel
        try {
            return Excel::download(new TunggakanExport($tagihan, $title), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate Excel: ' . $e->getMessage());
        }
    }

    /**
     * Cetak laporan tunggakan
     */
    public function printLaporan(Request $request)
    {
        $tahunAjaran = TahunAjaran::getActive();
        if (!$tahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }

        // Jika ada parameter santri_id, cetak hanya untuk 1 santri
        if ($request->has('santri_id')) {
            $santri = Santri::with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])->find($request->santri_id);
            if (!$santri) {
                return redirect()->back()->with('error', 'Santri tidak ditemukan');
            }            $tagihan = TagihanSantri::with('jenisTagihan')
                ->where('santri_id', $santri->id)
                ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
                ->get();
                
            $totalTunggakan = $tagihan->sum('sisa_tagihan');
            
            return view('keuangan.tunggakan.print_detail', compact('santri', 'tagihan', 'totalTunggakan', 'tahunAjaran'));
        }
        
        // Print berdasarkan tipe
        $tipe = $request->get('tipe', 'aktif');
        
        if ($tipe == 'mutasi') {
            $query = TagihanSantri::with(['santri', 'jenisTagihan'])
                ->whereHas('santri', function($q) {
                    $q->where('status', 'mutasi');
                });
            $title = 'Daftar Tunggakan Santri Mutasi';
            $viewName = 'print_mutasi';
        } elseif ($tipe == 'alumni') {
            $query = TagihanSantri::with(['santri', 'jenisTagihan'])
                ->whereHas('santri', function($q) {
                    $q->where('status', 'alumni');
                });
            $title = 'Daftar Tunggakan Santri Alumni';
            $viewName = 'print_alumni';
        } else {
            $query = TagihanSantri::with(['santri', 'jenisTagihan'])
                ->whereHas('santri', function($q) {
                    $q->where('status', 'aktif');
                })
                ->where('tahun_ajaran_id', $tahunAjaran->id);
            $title = 'Daftar Tunggakan Santri Aktif';
            $viewName = 'print_aktif';
        }
          $tunggakan = $query->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get()
            ->groupBy('santri_id');
              return view('keuangan.tunggakan.' . $viewName, compact('tunggakan', 'tahunAjaran'));
    }

    /**
     * Show automation management page for routine tagihan
     */
    public function automationManagement()
    {
        $currentTahunAjaran = TahunAjaran::getActive();
        $allTahunAjaran = TahunAjaran::orderBy('tahun_mulai', 'desc')->get();
        
        // Get routine jenis tagihan
        $routineJenisTagihan = JenisTagihan::where('kategori_tagihan', 'Rutin')->get();
        
        // Get statistics
        $totalActiveSantri = Santri::where('status', 'aktif')->count();
        
        return view('keuangan.tunggakan.automation', compact(
            'currentTahunAjaran',
            'allTahunAjaran',
            'routineJenisTagihan',
            'totalActiveSantri'
        ));
    }

    /**
     * Copy routine tagihan from one academic year to another
     */
    public function copyRoutineTagihan(Request $request)
    {
        $request->validate([
            'target_year_id' => 'required|exists:tahun_ajarans,id',
            'source_year_id' => 'nullable|exists:tahun_ajarans,id'
        ]);

        $targetYear = TahunAjaran::findOrFail($request->target_year_id);
        $sourceYear = $request->source_year_id ? TahunAjaran::findOrFail($request->source_year_id) : null;

        $result = $this->tagihanService->copyRoutineTagihanToNewYear($targetYear, $sourceYear);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Get preview of what tagihan will be copied
     */
    public function previewCopyRoutineTagihan(Request $request)
    {
        $request->validate([
            'target_year_id' => 'required|exists:tahun_ajarans,id',
            'source_year_id' => 'nullable|exists:tahun_ajarans,id'
        ]);

        $targetYear = TahunAjaran::findOrFail($request->target_year_id);
        
        if ($request->source_year_id) {
            $sourceYear = TahunAjaran::findOrFail($request->source_year_id);
        } else {
            $sourceYear = TahunAjaran::where('tahun_mulai', '<', $targetYear->tahun_mulai)
                ->orderBy('tahun_mulai', 'desc')
                ->first();
        }

        if (!$sourceYear) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran sebelumnya untuk disalin.'
            ]);
        }

        // Get active santri who had routine tagihan in source year
        $santrisWithRoutineTagihan = Santri::where('status', 'aktif')
            ->whereHas('tagihanSantris', function($q) use ($sourceYear) {
                $q->where('tahun_ajaran_id', $sourceYear->id)
                  ->whereHas('jenisTagihan', function($q2) {
                      $q2->where('kategori_tagihan', 'Rutin');
                  });
            })->count();

        // Get routine jenis tagihan
        $routineJenisTagihan = JenisTagihan::where('kategori_tagihan', 'Rutin')->get();

        // Estimate how many tagihan will be created
        $estimatedTagihan = 0;
        foreach ($routineJenisTagihan as $jenisTagihan) {
            if ($jenisTagihan->is_bulanan) {
                $estimatedTagihan += $santrisWithRoutineTagihan * 12; // 12 months
            } else {
                $estimatedTagihan += $santrisWithRoutineTagihan * 1; // Annual
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'source_year' => $sourceYear->nama,
                'target_year' => $targetYear->nama,
                'affected_santri' => $santrisWithRoutineTagihan,
                'routine_jenis_count' => $routineJenisTagihan->count(),
                'estimated_tagihan' => $estimatedTagihan,
                'routine_jenis_list' => $routineJenisTagihan->pluck('nama')->toArray()
            ]
        ]);
    }

    /**
     * Preview automation untuk modal popup
     */
    public function automationPreview(Request $request)
    {
        $request->validate([
            'tahun_ajaran_asal' => 'required|exists:tahun_ajaran,id',
            'tahun_ajaran_tujuan' => 'required|exists:tahun_ajaran,id',
            'kategori_tagihan' => 'required|array',
            'kategori_tagihan.*' => 'in:Rutin,Insidental'
        ]);

        $sourceYear = TahunAjaran::find($request->tahun_ajaran_asal);
        $targetYear = TahunAjaran::find($request->tahun_ajaran_tujuan);

        // Get active santri who had tagihan in source year
        $santrisWithTagihan = Santri::where('status', 'aktif')
            ->whereHas('tagihanSantris', function($q) use ($sourceYear, $request) {
                $q->where('tahun_ajaran_id', $sourceYear->id)
                  ->whereHas('jenisTagihan', function($q2) use ($request) {
                      $q2->whereIn('kategori_tagihan', $request->kategori_tagihan);
                  });
            })->get();

        // Get jenis tagihan that will be copied
        $jenisTagihanToCopy = JenisTagihan::whereIn('kategori_tagihan', $request->kategori_tagihan)->get();

        // Estimate how many tagihan will be created
        $estimatedTagihan = 0;
        foreach ($jenisTagihanToCopy as $jenisTagihan) {
            if ($jenisTagihan->is_bulanan) {
                $estimatedTagihan += $santrisWithTagihan->count() * 12; // 12 months
            } else {
                $estimatedTagihan += $santrisWithTagihan->count() * 1; // Annual
            }
        }

        // Generate preview HTML
        $previewHtml = '<div class="space-y-4">';
        $previewHtml .= '<div class="grid grid-cols-2 gap-4">';
        $previewHtml .= '<div class="bg-blue-50 p-3 rounded"><strong>Tahun Ajaran Asal:</strong><br>' . $sourceYear->nama_tahun_ajaran . '</div>';
        $previewHtml .= '<div class="bg-green-50 p-3 rounded"><strong>Tahun Ajaran Tujuan:</strong><br>' . $targetYear->nama_tahun_ajaran . '</div>';
        $previewHtml .= '</div>';
        $previewHtml .= '<div class="grid grid-cols-2 gap-4">';
        $previewHtml .= '<div class="bg-yellow-50 p-3 rounded"><strong>Santri yang Terpengaruh:</strong><br>' . $santrisWithTagihan->count() . ' santri</div>';
        $previewHtml .= '<div class="bg-purple-50 p-3 rounded"><strong>Estimasi Tagihan:</strong><br>' . number_format($estimatedTagihan) . ' tagihan</div>';
        $previewHtml .= '</div>';
        $previewHtml .= '<div class="bg-gray-50 p-3 rounded">';
        $previewHtml .= '<strong>Jenis Tagihan yang akan disalin:</strong><ul class="mt-2 space-y-1">';
        foreach ($jenisTagihanToCopy as $jenis) {
            $previewHtml .= '<li>• ' . $jenis->nama . ' (' . $jenis->kategori_tagihan . ')</li>';
        }
        $previewHtml .= '</ul></div>';
        $previewHtml .= '</div>';

        return response()->json([
            'success' => true,
            'preview_html' => $previewHtml
        ]);
    }

    /**
     * Execute automation untuk modal popup
     */
    public function automationExecute(Request $request)
    {
        $request->validate([
            'tahun_ajaran_asal' => 'required|exists:tahun_ajaran,id',
            'tahun_ajaran_tujuan' => 'required|exists:tahun_ajaran,id',
            'kategori_tagihan' => 'required|array',
            'kategori_tagihan.*' => 'in:Rutin,Insidental'
        ]);

        try {
            $result = $this->tagihanService->copyTagihanBetweenYears(
                $request->tahun_ajaran_asal,
                $request->tahun_ajaran_tujuan,
                $request->kategori_tagihan,
                $request->has('replace_existing')
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Otomatisasi berhasil dijalankan!',
                    'created_count' => $result['created_count'],
                    'updated_count' => $result['updated_count'] ?? 0
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}

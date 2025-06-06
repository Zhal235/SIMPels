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

class TunggakanController extends Controller
{
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
          // Mendapatkan semua tagihan yang belum lunas
        $tagihan = TagihanSantri::with('jenisTagihan')
            ->where('santri_id', $santri_id)
            ->where('status', 'aktif')
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->get();
            
        $totalTunggakan = $tagihan->sum('sisa_tagihan');
        
        return view('keuangan.tunggakan.detail', compact('santri', 'tagihan', 'totalTunggakan', 'tahunAjaran'));
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
}

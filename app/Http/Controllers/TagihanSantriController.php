<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TagihanSantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }

        $kelasFilter = $request->get('kelas');
        $asramaFilter = $request->get('asrama');

        $santrisQuery = Santri::where('status', 'aktif')
            ->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama']);

        if ($kelasFilter) {
            $santrisQuery->whereHas('kelasRelasi', function($q) use ($kelasFilter) {
                $q->where('nama', $kelasFilter);
            });
        }

        if ($asramaFilter) {
            $santrisQuery->whereHas('asrama_anggota_terakhir.asrama', function($q) use ($asramaFilter) {
                $q->where('nama_asrama', $asramaFilter);
            });
        }

        $santris = $santrisQuery->get()->map(function($santri) use ($activeTahunAjaran) {
            $kelas = $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Belum ada kelas';
            $asrama = $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama_asrama : 'Belum ada asrama';

            // Ambil tagihan santri untuk tahun ajaran aktif
            $tagihanSantris = TagihanSantri::where('santri_id', $santri->id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->with(['jenisTagihan', 'transaksis'])
                ->get();

            $summaryRutin = ['total_tagihan' => 0, 'total_pembayaran' => 0, 'sisa_tagihan' => 0];
            $summaryInsidentil = ['total_tagihan' => 0, 'total_pembayaran' => 0, 'sisa_tagihan' => 0];
            
            $tagihanRutin = [];
            $tagihanInsidentil = [];

            // Group tagihan rutin by month
            $tagihanRutinByMonth = [];

            foreach ($tagihanSantris as $tagihan) {
                if ($tagihan->jenisTagihan->kategori_tagihan === 'Rutin') {
                    $bulanKey = $tagihan->bulan ?? date('Y-m');
                    
                    if (!isset($tagihanRutinByMonth[$bulanKey])) {
                        $tagihanRutinByMonth[$bulanKey] = [
                            'nama_bulan_tahun' => $tagihan->bulan_tahun,
                            'bulan' => $bulanKey,
                            'tagihan_items' => []
                        ];
                    }
                    
                    $tagihanRutinByMonth[$bulanKey]['tagihan_items'][] = [
                        'id' => $tagihan->id,
                        'nama_tagihan' => $tagihan->jenisTagihan->nama,
                        'nominal' => $tagihan->nominal_tagihan,
                        'total_dibayar' => $tagihan->nominal_dibayar,
                        'sisa_bayar' => $tagihan->sisa_tagihan,
                        'status_pembayaran' => $tagihan->status_pembayaran,
                        'persentase' => $tagihan->persentase_pembayaran
                    ];
                    
                    $summaryRutin['total_tagihan'] += $tagihan->nominal_tagihan;
                    $summaryRutin['total_pembayaran'] += $tagihan->nominal_dibayar;
                } else {
                    $tagihanInsidentil[] = [
                        'id' => $tagihan->id,
                        'nama_tagihan' => $tagihan->jenisTagihan->nama,
                        'nominal' => $tagihan->nominal_tagihan,
                        'total_dibayar' => $tagihan->nominal_dibayar,
                        'sisa_bayar' => $tagihan->sisa_tagihan,
                        'status_pembayaran' => $tagihan->status_pembayaran,
                        'persentase' => $tagihan->persentase_pembayaran
                    ];
                    
                    $summaryInsidentil['total_tagihan'] += $tagihan->nominal_tagihan;
                    $summaryInsidentil['total_pembayaran'] += $tagihan->nominal_dibayar;
                }
            }

            // Convert grouped rutin to array for consistency
            $tagihanRutin = array_values($tagihanRutinByMonth);

            $summaryRutin['sisa_tagihan'] = $summaryRutin['total_tagihan'] - $summaryRutin['total_pembayaran'];
            $summaryInsidentil['sisa_tagihan'] = $summaryInsidentil['total_tagihan'] - $summaryInsidentil['total_pembayaran'];

            return [
                'id' => $santri->id,
                'nama_santri' => $santri->nama_santri,
                'nis' => $santri->nis,
                'kelas' => $kelas,
                'asrama' => $asrama,
                'tagihan_rutin' => $tagihanRutin,
                'summary_rutin' => $summaryRutin,
                'tagihan_insidentil' => $tagihanInsidentil,
                'summary_insidentil' => $summaryInsidentil,
            ];
        });

        // Get unique kelas and asrama for filters
        $allKelas = Santri::where('status', 'aktif')
            ->with('kelasRelasi')
            ->get()
            ->flatMap(function($santri) {
                return $santri->kelasRelasi->pluck('nama');
            })
            ->unique()
            ->sort()
            ->values();

        $allAsrama = Santri::where('status', 'aktif')
            ->with('asrama_anggota_terakhir.asrama')
            ->get()
            ->map(function($santri) {
                return $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama_asrama : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('keuangan.tagihan_santri.index', compact(
            'santris', 
            'activeTahunAjaran',
            'kelasFilter',
            'asramaFilter',
            'allKelas',
            'allAsrama'
        ));
    }

    /**
     * Get detailed tagihan for a specific santri
     */
    public function show($santriId)
    {
        $santri = Santri::with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])->findOrFail($santriId);
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 400);
        }

        // Get TagihanSantri untuk santri ini
        $tagihanSantris = TagihanSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->with(['jenisTagihan', 'transaksis'])
            ->get()
            ->map(function($tagihan) {
                return [
                    'id' => $tagihan->id,
                    'nama_tagihan' => $tagihan->jenisTagihan->nama,
                    'kategori' => $tagihan->jenisTagihan->kategori_tagihan,
                    'bulan_tahun' => $tagihan->bulan_tahun,
                    'nominal_tagihan' => $tagihan->nominal_tagihan,
                    'nominal_dibayar' => $tagihan->nominal_dibayar,
                    'sisa_tagihan' => $tagihan->sisa_tagihan,
                    'status_pembayaran' => $tagihan->status_pembayaran,
                    'persentase_pembayaran' => $tagihan->persentase_pembayaran,
                    'keterangan' => $tagihan->keterangan,
                    'transaksis' => $tagihan->transaksis->map(function($t) {
                        return [
                            'id' => $t->id,
                            'nominal' => $t->nominal,
                            'tanggal' => $t->tanggal->format('d/m/Y'),
                            'keterangan' => $t->keterangan
                        ];
                    })
                ];
            });

        return response()->json([
            'santri' => [
                'id' => $santri->id,
                'nama_santri' => $santri->nama_santri,
                'nis' => $santri->nis,
                'kelas' => $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Belum ada kelas',
                'asrama' => $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama_asrama : 'Belum ada asrama'
            ],
            'tagihan' => $tagihanSantris
        ]);
    }

    /**
     * Export tagihan santri to Excel
     */
    public function export(Request $request)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }

        // TODO: Implementasi export Excel menggunakan TagihanSantri
        return redirect()->back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
    }
}

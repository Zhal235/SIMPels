<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;
use App\Models\KeringananTagihan;

class TagihanController extends Controller
{
    /**
     * Get list of tagihan for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTagihanList(Request $request)
    {
        $user = $request->user();
        $santriId = $request->input('santri_id');
        
        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran aktif'
            ], 400);
        }

        // Get santri IDs associated with this user
        $query = Santri::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email_orangtua', $user->email);
            });
        
        // Filter by santriId if provided
        if ($santriId) {
            $query->where('id', $santriId);
        }
        
        $santris = $query->pluck('id');
        
        if ($santris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri yang terkait dengan akun ini'
            ], 404);
        }

        // Get tagihan for the santri
        $tagihan = TagihanSantri::whereIn('santri_id', $santris)
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->with([
                'santri:id,nama_santri,nis',
                'jenisTagihan:id,nama,kategori_tagihan,deskripsi,is_bulanan', 
                'transaksis'
            ])
            ->get()
            ->map(function($tagihan) {
                return [
                    'id' => $tagihan->id,
                    'santri' => [
                        'id' => $tagihan->santri->id,
                        'nama' => $tagihan->santri->nama_santri,
                        'nis' => $tagihan->santri->nis,
                    ],
                    'jenis_tagihan' => [
                        'id' => $tagihan->jenisTagihan->id,
                        'nama' => $tagihan->jenisTagihan->nama,
                        'kategori' => $tagihan->jenisTagihan->kategori_tagihan,
                        'is_bulanan' => $tagihan->jenisTagihan->is_bulanan,
                    ],
                    'bulan' => $tagihan->bulan,
                    'bulan_tahun' => $tagihan->bulan_tahun,
                    'nominal_tagihan' => (int)$tagihan->nominal_tagihan,
                    'nominal_dibayar' => (int)$tagihan->nominal_dibayar,
                    'nominal_keringanan' => (int)$tagihan->nominal_keringanan,
                    'sisa_tagihan' => (int)$tagihan->sisa_tagihan,
                    'status_pembayaran' => $tagihan->status_pembayaran,
                    'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
                    'is_jatuh_tempo' => $tagihan->is_jatuh_tempo,
                    'persentase_pembayaran' => $tagihan->persentase_pembayaran,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }

    /**
     * Get detailed information about a specific tagihan
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTagihanDetail(Request $request, $id)
    {
        $user = $request->user();

        // Get santri IDs associated with this user
        $santriIds = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->pluck('id');

        // Get tagihan detail with protection to ensure it belongs to user's santri
        $tagihan = TagihanSantri::where('id', $id)
            ->whereIn('santri_id', $santriIds)
            ->with(['jenisTagihan', 'santri:id,nama_santri,nis', 'transaksis', 'tahunAjaran'])
            ->first();

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan atau Anda tidak memiliki akses'
            ], 404);
        }

        // Format the response
        $data = [
            'id' => $tagihan->id,
            'santri' => [
                'id' => $tagihan->santri->id,
                'nama' => $tagihan->santri->nama_santri,
                'nis' => $tagihan->santri->nis,
            ],
            'jenis_tagihan' => [
                'id' => $tagihan->jenisTagihan->id,
                'nama' => $tagihan->jenisTagihan->nama,
                'kategori' => $tagihan->jenisTagihan->kategori_tagihan, 
                'deskripsi' => $tagihan->jenisTagihan->deskripsi,
                'is_bulanan' => $tagihan->jenisTagihan->is_bulanan,
            ],
            'tahun_ajaran' => [
                'id' => $tagihan->tahunAjaran->id,
                'nama' => $tagihan->tahunAjaran->nama,
            ],
            'bulan' => $tagihan->bulan,
            'bulan_tahun' => $tagihan->bulan_tahun,
            'nominal_tagihan' => (int)$tagihan->nominal_tagihan,
            'nominal_dibayar' => (int)$tagihan->nominal_dibayar,
            'nominal_keringanan' => (int)$tagihan->nominal_keringanan,
            'sisa_tagihan' => (int)$tagihan->sisa_tagihan,
            'status_pembayaran' => $tagihan->status_pembayaran,
            'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
            'is_jatuh_tempo' => $tagihan->is_jatuh_tempo,
            'persentase_pembayaran' => $tagihan->persentase_pembayaran,
            'transaksi' => $tagihan->transaksis->map(function($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'nominal' => (int)$transaksi->nominal,
                    'tanggal' => $transaksi->tanggal->format('Y-m-d'),
                    'keterangan' => $transaksi->keterangan,
                    'tipe_pembayaran' => $transaksi->tipe_pembayaran,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get summary of tagihan for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTagihanSummary(Request $request)
    {
        $user = $request->user();
        $santriId = $request->input('santri_id');

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran aktif'
            ], 400);
        }

        // Get santri IDs associated with this user
        $query = Santri::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email_orangtua', $user->email);
            });
        
        // Filter by santriId if provided
        if ($santriId) {
            $query->where('id', $santriId);
        }
        
        $santris = $query->get(['id', 'nama_santri', 'nis']);
        
        if ($santris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri yang terkait dengan akun ini'
            ], 404);
        }

        $summaryData = [];
        
        foreach ($santris as $santri) {
            // Get tagihan rutin
            $tagihanRutin = TagihanSantri::where('santri_id', $santri->id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->whereHas('jenisTagihan', function($query) {
                    $query->where('kategori_tagihan', 'Rutin');
                })
                ->get();

            // Get tagihan insidentil
            $tagihanInsidentil = TagihanSantri::where('santri_id', $santri->id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->whereHas('jenisTagihan', function($query) {
                    $query->where('kategori_tagihan', 'Insidentil');
                })
                ->get();

            // Calculate summary
            $summaryRutin = [
                'total_tagihan' => $tagihanRutin->sum('nominal_tagihan'),
                'total_dibayar' => $tagihanRutin->sum('nominal_dibayar'),
                'total_keringanan' => $tagihanRutin->sum('nominal_keringanan'),
                'sisa_tagihan' => $tagihanRutin->sum('nominal_tagihan') - $tagihanRutin->sum('nominal_dibayar') - $tagihanRutin->sum('nominal_keringanan'),
                'jumlah_tagihan' => $tagihanRutin->count(),
                'jumlah_lunas' => $tagihanRutin->where('status_pembayaran', 'lunas')->count(),
            ];

            $summaryInsidentil = [
                'total_tagihan' => $tagihanInsidentil->sum('nominal_tagihan'),
                'total_dibayar' => $tagihanInsidentil->sum('nominal_dibayar'),
                'total_keringanan' => $tagihanInsidentil->sum('nominal_keringanan'),
                'sisa_tagihan' => $tagihanInsidentil->sum('nominal_tagihan') - $tagihanInsidentil->sum('nominal_dibayar') - $tagihanInsidentil->sum('nominal_keringanan'),
                'jumlah_tagihan' => $tagihanInsidentil->count(),
                'jumlah_lunas' => $tagihanInsidentil->where('status_pembayaran', 'lunas')->count(),
            ];

            $totalSummary = [
                'total_tagihan' => $summaryRutin['total_tagihan'] + $summaryInsidentil['total_tagihan'],
                'total_dibayar' => $summaryRutin['total_dibayar'] + $summaryInsidentil['total_dibayar'],
                'total_keringanan' => $summaryRutin['total_keringanan'] + $summaryInsidentil['total_keringanan'],
                'sisa_tagihan' => $summaryRutin['sisa_tagihan'] + $summaryInsidentil['sisa_tagihan'],
                'jumlah_tagihan' => $summaryRutin['jumlah_tagihan'] + $summaryInsidentil['jumlah_tagihan'],
                'jumlah_lunas' => $summaryRutin['jumlah_lunas'] + $summaryInsidentil['jumlah_lunas'],
            ];

            $summaryData[] = [
                'santri' => [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                ],
                'summary_rutin' => $summaryRutin,
                'summary_insidentil' => $summaryInsidentil,
                'summary_total' => $totalSummary,
                'tahun_ajaran' => [
                    'id' => $activeTahunAjaran->id,
                    'nama' => $activeTahunAjaran->nama,
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $summaryData
        ]);
    }

    /**
     * Get list of tunggakan (unpaid bills) for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTunggakanList(Request $request)
    {
        $user = $request->user();
        $santriId = $request->input('santri_id');
        
        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran aktif'
            ], 400);
        }

        // Get santri IDs associated with this user
        $query = Santri::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email_orangtua', $user->email);
            });
        
        // Filter by santriId if provided
        if ($santriId) {
            $query->where('id', $santriId);
        }
        
        $santris = $query->pluck('id');
        
        if ($santris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri yang terkait dengan akun ini'
            ], 404);
        }
        
        // Current date for tempo check
        $today = now()->format('Y-m-d');

        // Get tagihan that are not fully paid and have passed their due date or from previous years
        $tunggakan = TagihanSantri::whereIn('santri_id', $santris)
            ->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
            ->where(function($query) use ($today, $activeTahunAjaran) {
                // Include tagihan that are past due
                $query->where('tanggal_jatuh_tempo', '<', $today)
                    // Or from previous years (automatically considered as tunggakan)
                    ->orWhere('tahun_ajaran_id', '!=', $activeTahunAjaran->id);
            })
            ->with([
                'santri:id,nama_santri,nis',
                'jenisTagihan:id,nama,kategori_tagihan',
                'tahunAjaran:id,nama'
            ])
            ->get()
            ->map(function($tagihan) {
                return [
                    'id' => $tagihan->id,
                    'santri' => [
                        'id' => $tagihan->santri->id,
                        'nama' => $tagihan->santri->nama_santri,
                        'nis' => $tagihan->santri->nis,
                    ],
                    'jenis_tagihan' => [
                        'id' => $tagihan->jenisTagihan->id,
                        'nama' => $tagihan->jenisTagihan->nama,
                        'kategori' => $tagihan->jenisTagihan->kategori_tagihan,
                    ],
                    'tahun_ajaran' => [
                        'id' => $tagihan->tahunAjaran->id,
                        'nama' => $tagihan->tahunAjaran->nama,
                    ],
                    'bulan' => $tagihan->bulan,
                    'bulan_tahun' => $tagihan->bulan_tahun,
                    'nominal_tagihan' => (int)$tagihan->nominal_tagihan,
                    'nominal_dibayar' => (int)$tagihan->nominal_dibayar,
                    'nominal_keringanan' => (int)$tagihan->nominal_keringanan,
                    'sisa_tagihan' => (int)$tagihan->sisa_tagihan,
                    'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tunggakan
        ]);
    }

    /**
     * Get details of a specific tunggakan
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTunggakanDetail(Request $request, $id)
    {
        // This is just a wrapper around getTagihanDetail since they're the same data
        return $this->getTagihanDetail($request, $id);
    }

    /**
     * Get list of keringanan for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKeringananList(Request $request)
    {
        $user = $request->user();
        $santriId = $request->input('santri_id');
        
        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran aktif'
            ], 400);
        }

        // Get santri IDs associated with this user
        $query = Santri::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email_orangtua', $user->email);
            });
        
        // Filter by santriId if provided
        if ($santriId) {
            $query->where('id', $santriId);
        }
        
        $santris = $query->pluck('id');
        
        if ($santris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri yang terkait dengan akun ini'
            ], 404);
        }

        // Get keringanan for the santris
        $keringanan = KeringananTagihan::whereIn('santri_id', $santris)
            ->where('status', 'aktif')
            ->with([
                'santri:id,nama_santri,nis',
                'jenisTagihan:id,nama,deskripsi',
                'tahunAjaran:id,nama',
                'santriTertanggung:id,nama_santri,nis'
            ])
            ->get()
            ->map(function($keringanan) {
                $data = [
                    'id' => $keringanan->id,
                    'santri' => [
                        'id' => $keringanan->santri->id,
                        'nama' => $keringanan->santri->nama_santri,
                        'nis' => $keringanan->santri->nis,
                    ],
                    'jenis_keringanan' => $keringanan->jenis_keringanan,
                    'nilai_potongan' => (int)$keringanan->nilai_potongan,
                    'keterangan' => $keringanan->keterangan,
                    'status' => $keringanan->status,
                    'tanggal_mulai' => $keringanan->tanggal_mulai,
                    'tanggal_selesai' => $keringanan->tanggal_selesai,
                    'tahun_ajaran' => [
                        'id' => $keringanan->tahunAjaran->id,
                        'nama' => $keringanan->tahunAjaran->nama,
                    ],
                ];
                
                // Include jenis_tagihan if available
                if ($keringanan->jenisTagihan) {
                    $data['jenis_tagihan'] = [
                        'id' => $keringanan->jenisTagihan->id,
                        'nama' => $keringanan->jenisTagihan->nama,
                        'deskripsi' => $keringanan->jenisTagihan->deskripsi,
                    ];
                }
                
                // Include santri_tertanggung if available (for bayar_satu_gratis_satu type)
                if ($keringanan->santriTertanggung) {
                    $data['santri_tertanggung'] = [
                        'id' => $keringanan->santriTertanggung->id,
                        'nama' => $keringanan->santriTertanggung->nama_santri,
                        'nis' => $keringanan->santriTertanggung->nis,
                    ];
                }
                
                return $data;
            });

        return response()->json([
            'success' => true,
            'data' => $keringanan
        ]);
    }

    /**
     * Get details of a specific keringanan
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKeringananDetail(Request $request, $id)
    {
        $user = $request->user();

        // Get santri IDs associated with this user
        $santriIds = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->pluck('id');

        // Get keringanan detail with protection to ensure it belongs to user's santri
        $keringanan = KeringananTagihan::where('id', $id)
            ->whereIn('santri_id', $santriIds)
            ->with([
                'santri:id,nama_santri,nis',
                'jenisTagihan:id,nama,deskripsi',
                'tahunAjaran:id,nama',
                'santriTertanggung:id,nama_santri,nis'
            ])
            ->first();

        if (!$keringanan) {
            return response()->json([
                'success' => false,
                'message' => 'Keringanan tidak ditemukan atau Anda tidak memiliki akses'
            ], 404);
        }

        // Format the response
        $data = [
            'id' => $keringanan->id,
            'santri' => [
                'id' => $keringanan->santri->id,
                'nama' => $keringanan->santri->nama_santri,
                'nis' => $keringanan->santri->nis,
            ],
            'jenis_keringanan' => $keringanan->jenis_keringanan,
            'nilai_potongan' => (int)$keringanan->nilai_potongan,
            'keterangan' => $keringanan->keterangan,
            'status' => $keringanan->status,
            'tanggal_mulai' => $keringanan->tanggal_mulai,
            'tanggal_selesai' => $keringanan->tanggal_selesai,
            'tahun_ajaran' => [
                'id' => $keringanan->tahunAjaran->id,
                'nama' => $keringanan->tahunAjaran->nama,
            ],
        ];
        
        // Include jenis_tagihan if available
        if ($keringanan->jenisTagihan) {
            $data['jenis_tagihan'] = [
                'id' => $keringanan->jenisTagihan->id,
                'nama' => $keringanan->jenisTagihan->nama,
                'deskripsi' => $keringanan->jenisTagihan->deskripsi,
            ];
        }
        
        // Include santri_tertanggung if available (for bayar_satu_gratis_satu type)
        if ($keringanan->santriTertanggung) {
            $data['santri_tertanggung'] = [
                'id' => $keringanan->santriTertanggung->id,
                'nama' => $keringanan->santriTertanggung->nama_santri,
                'nis' => $keringanan->santriTertanggung->nis,
            ];
        }
        
        // Get the affected tagihan
        $affectedTagihan = TagihanSantri::where('santri_id', $keringanan->santri_id)
            ->where('tahun_ajaran_id', $keringanan->tahun_ajaran_id)
            ->where('nominal_keringanan', '>', 0);
            
        // Filter by jenis_tagihan if applicable
        if ($keringanan->jenis_tagihan_id) {
            $affectedTagihan->where('jenis_tagihan_id', $keringanan->jenis_tagihan_id);
        }
        
        $tagihanList = $affectedTagihan->get()->map(function($tagihan) {
            return [
                'id' => $tagihan->id,
                'jenis_tagihan' => $tagihan->jenisTagihan->nama,
                'bulan_tahun' => $tagihan->bulan_tahun,
                'nominal_tagihan' => (int)$tagihan->nominal_tagihan,
                'nominal_keringanan' => (int)$tagihan->nominal_keringanan,
                'nominal_harus_dibayar' => (int)$tagihan->nominal_harus_dibayar,
                'status_pembayaran' => $tagihan->status_pembayaran,
            ];
        });
        
        $data['tagihan_affected'] = $tagihanList;
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

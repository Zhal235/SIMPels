<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Transaksi;
use App\Models\TahunAjaran;

class TransaksiController extends Controller
{
    /**
     * Get list of transaksi for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransaksiList(Request $request)
    {
        $user = $request->user();
        $santriId = $request->input('santri_id');
        
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

        // Get all transaksi for the santri
        $transaksi = Transaksi::whereIn('santri_id', $santris)
            ->with([
                'santri:id,nama_santri,nis',
                'tagihanSantri.jenisTagihan:id,nama,kategori_tagihan',
                'tahunAjaran:id,nama'
            ])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'santri' => [
                        'id' => $transaksi->santri->id,
                        'nama' => $transaksi->santri->nama_santri,
                        'nis' => $transaksi->santri->nis,
                    ],
                    'jenis_tagihan' => $transaksi->tagihanSantri ? [
                        'id' => $transaksi->tagihanSantri->jenisTagihan->id,
                        'nama' => $transaksi->tagihanSantri->jenisTagihan->nama,
                        'kategori' => $transaksi->tagihanSantri->jenisTagihan->kategori_tagihan,
                    ] : null,
                    'bulan' => $transaksi->tagihanSantri ? $transaksi->tagihanSantri->bulan : null,
                    'bulan_tahun' => $transaksi->tagihanSantri ? $transaksi->tagihanSantri->bulan_tahun : null,
                    'tagihan_santri_id' => $transaksi->tagihan_santri_id,
                    'tahun_ajaran' => [
                        'id' => $transaksi->tahunAjaran->id,
                        'nama' => $transaksi->tahunAjaran->nama,
                    ],
                    'nominal' => (int)$transaksi->nominal,
                    'tanggal' => $transaksi->tanggal->format('Y-m-d'),
                    'keterangan' => $transaksi->keterangan,
                    'tipe_pembayaran' => $transaksi->tipe_pembayaran,
                ];
            });
            
        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    /**
     * Get detailed information about a specific transaksi
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransaksiDetail(Request $request, $id)
    {
        $user = $request->user();

        // Get santri IDs associated with this user
        $santriIds = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->pluck('id');

        // Get transaksi detail with protection to ensure it belongs to user's santri
        $transaksi = Transaksi::where('id', $id)
            ->whereIn('santri_id', $santriIds)
            ->with([
                'santri:id,nama_santri,nis',
                'tagihanSantri.jenisTagihan:id,nama,kategori_tagihan,deskripsi',
                'tahunAjaran:id,nama'
            ])
            ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan atau Anda tidak memiliki akses'
            ], 404);
        }

        // Format the response
        $data = [
            'id' => $transaksi->id,
            'santri' => [
                'id' => $transaksi->santri->id,
                'nama' => $transaksi->santri->nama_santri,
                'nis' => $transaksi->santri->nis,
            ],
            'tagihan_santri_id' => $transaksi->tagihan_santri_id,
            'tahun_ajaran' => [
                'id' => $transaksi->tahunAjaran->id,
                'nama' => $transaksi->tahunAjaran->nama,
            ],
            'nominal' => (int)$transaksi->nominal,
            'tanggal' => $transaksi->tanggal->format('Y-m-d'),
            'keterangan' => $transaksi->keterangan,
            'tipe_pembayaran' => $transaksi->tipe_pembayaran,
        ];

        // Include tagihan data if available
        if ($transaksi->tagihanSantri) {
            $data['tagihan'] = [
                'id' => $transaksi->tagihanSantri->id,
                'jenis_tagihan' => [
                    'id' => $transaksi->tagihanSantri->jenisTagihan->id,
                    'nama' => $transaksi->tagihanSantri->jenisTagihan->nama,
                    'kategori' => $transaksi->tagihanSantri->jenisTagihan->kategori_tagihan,
                    'deskripsi' => $transaksi->tagihanSantri->jenisTagihan->deskripsi,
                ],
                'bulan' => $transaksi->tagihanSantri->bulan,
                'bulan_tahun' => $transaksi->tagihanSantri->bulan_tahun,
                'nominal_tagihan' => (int)$transaksi->tagihanSantri->nominal_tagihan,
                'nominal_dibayar' => (int)$transaksi->tagihanSantri->nominal_dibayar,
                'nominal_keringanan' => (int)$transaksi->tagihanSantri->nominal_keringanan,
                'sisa_tagihan' => (int)$transaksi->tagihanSantri->sisa_tagihan,
                'status_pembayaran' => $transaksi->tagihanSantri->status_pembayaran,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\AsramaAnggota;
use App\Models\Asrama;

class AsramaController extends Controller
{
    /**
     * Get asrama information for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAsramaInfo(Request $request)
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

        // Get asrama information for each santri
        $asramaInfo = [];
        
        foreach ($santris as $id) {
            $santri = Santri::with(['asrama_anggota_terakhir.asrama'])->find($id);
            
            if (!$santri) {
                continue;
            }

            $asramaAnggota = $santri->asrama_anggota_terakhir;
            
            if (!$asramaAnggota || !$asramaAnggota->asrama) {
                $asramaInfo[] = [
                    'santri_id' => $santri->id,
                    'santri_nama' => $santri->nama_santri,
                    'santri_nis' => $santri->nis,
                    'asrama_info' => null
                ];
                continue;
            }
            
            // Get asrama information
            $asrama = $asramaAnggota->asrama;
            
            $asramaInfo[] = [
                'santri_id' => $santri->id,
                'santri_nama' => $santri->nama_santri,
                'santri_nis' => $santri->nis,
                'asrama_info' => [
                    'id' => $asrama->id,
                    'kode' => $asrama->kode,
                    'nama' => $asrama->nama,
                    'jenis_asrama' => $asrama->jenis_asrama,
                    'kapasitas' => $asrama->kapasitas,
                    'tanggal_masuk' => $asramaAnggota->tanggal_masuk ? $asramaAnggota->tanggal_masuk->format('Y-m-d') : null,
                    'tanggal_keluar' => $asramaAnggota->tanggal_keluar ? $asramaAnggota->tanggal_keluar->format('Y-m-d') : null,
                    'status' => $asramaAnggota->tanggal_keluar ? 'Tidak Aktif' : 'Aktif',
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $asramaInfo
        ]);
    }
}

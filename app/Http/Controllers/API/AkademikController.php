<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\KelasAnggota;
use App\Models\TahunAjaran;

class AkademikController extends Controller
{
    /**
     * Get academic information for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAkademikInfo(Request $request)
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
        
        $santris = $query->with(['kelasRelasi'])->get();
        
        if ($santris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri yang terkait dengan akun ini'
            ], 404);
        }

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran aktif'
            ], 400);
        }

        $akademikData = [];
        
        foreach ($santris as $santri) {
            // Get kelas anggota data
            $kelasAnggota = KelasAnggota::where('santri_id', $santri->id)
                ->with(['kelas'])
                ->orderBy('tanggal_masuk', 'desc')
                ->get();
                
            // Format kelas history
            $kelasHistory = $kelasAnggota->map(function($item) {
                return [
                    'id' => $item->id,
                    'kelas' => [
                        'id' => $item->kelas->id,
                        'nama' => $item->kelas->nama,
                        'tingkat' => $item->kelas->tingkat,
                    ],
                    'tanggal_masuk' => $item->tanggal_masuk ? $item->tanggal_masuk->format('Y-m-d') : null,
                    'tanggal_keluar' => $item->tanggal_keluar ? $item->tanggal_keluar->format('Y-m-d') : null,
                    'status' => $item->tanggal_keluar ? 'Tidak Aktif' : 'Aktif',
                ];
            });
                
            // Get current kelas (latest without tanggal_keluar)
            $currentKelas = $kelasAnggota->first(function($item) {
                return $item->tanggal_keluar === null;
            });
            
            $akademikData[] = [
                'santri_id' => $santri->id,
                'santri_nama' => $santri->nama_santri,
                'santri_nis' => $santri->nis,
                'kelas_aktif' => $currentKelas ? [
                    'id' => $currentKelas->kelas->id,
                    'nama' => $currentKelas->kelas->nama,
                    'tingkat' => $currentKelas->kelas->tingkat,
                ] : null,
                'kelas_history' => $kelasHistory,
            ];
        }

        return response()->json([
            'success' => true,
            'tahun_ajaran' => [
                'id' => $activeTahunAjaran->id,
                'nama' => $activeTahunAjaran->nama,
                'tahun_mulai' => $activeTahunAjaran->tahun_mulai,
                'tahun_selesai' => $activeTahunAjaran->tahun_selesai,
            ],
            'data' => $akademikData
        ]);
    }
}

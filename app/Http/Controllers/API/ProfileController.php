<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\TahunAjaran;

class ProfileController extends Controller
{
    /**
     * Get list of santri connected to authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSantriList(Request $request)
    {
        $user = $request->user();
        
        // Get santri connected to this user
        $santris = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])
            ->get()
            ->map(function($santri) {
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'kelas' => $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-',
                    'asrama' => $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama : '-',
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : null,
                    'jenis_kelamin' => $santri->jenis_kelamin,
                    'status' => $santri->status
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $santris
        ]);
    }
    
    /**
     * Get detailed information about a specific santri
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSantriDetail(Request $request, $id)
    {
        $user = $request->user();
        
        // Check if santri belongs to this user
        $santri = Santri::where('id', $id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email_orangtua', $user->email);
            })
            ->with([
                'kelasRelasi', 
                'asrama_anggota_terakhir.asrama',
                'pekerjaanAyah',
                'pekerjaanIbu'
            ])
            ->first();
        
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan atau Anda tidak memiliki akses'
            ], 404);
        }

        // Format data to return
        $data = [
            'id' => $santri->id,
            'nis' => $santri->nis,
            'nama_santri' => $santri->nama_santri,
            'jenis_kelamin' => $santri->jenis_kelamin,
            'tempat_lahir' => $santri->tempat_lahir,
            'tanggal_lahir' => $santri->tanggal_lahir ? $santri->tanggal_lahir->format('Y-m-d') : null,
            'foto' => $santri->foto ? asset('storage/' . $santri->foto) : null,
            'status' => $santri->status,
            'pendidikan' => [
                'kelas' => $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-',
            ],
            'asrama' => [
                'nama' => $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama : '-',
            ],
            'orangtua' => [
                'nama_ayah' => $santri->nama_ayah,
                'pekerjaan_ayah' => $santri->pekerjaanAyah ? $santri->pekerjaanAyah->nama_pekerjaan : '-',
                'hp_ayah' => $santri->hp_ayah,
                'nama_ibu' => $santri->nama_ibu,
                'pekerjaan_ibu' => $santri->pekerjaanIbu ? $santri->pekerjaanIbu->nama_pekerjaan : '-',
                'hp_ibu' => $santri->hp_ibu,
            ],
            'alamat' => [
                'alamat' => $santri->alamat,
                'desa' => $santri->desa,
                'kecamatan' => $santri->kecamatan,
                'kabupaten' => $santri->kabupaten,
                'provinsi' => $santri->provinsi,
                'kode_pos' => $santri->kode_pos,
            ],
            'informasi_lainnya' => [
                'no_bpjs' => $santri->no_bpjs,
                'no_kip' => $santri->no_kip,
                'no_pkh' => $santri->no_pkh,
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dompet;
use App\Models\Santri;
use App\Models\TransaksiDompet;
use Illuminate\Support\Facades\DB;

class WaliSantriDompetController extends Controller
{
    /**
     * Get dompet information for a santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDompetInfo(Request $request)
    {
        // Get santris related to the authenticated user
        $user = $request->user();
        $santris = Santri::where('user_id', $user->id)
                    ->orWhere('email_orangtua', $user->email)
                    ->pluck('id');
        
        // Get dompets for all related santris
        $dompets = Dompet::whereIn('santri_id', $santris)
                    ->with('santri')
                    ->get()
                    ->map(function($dompet) {
                        return [
                            'id' => $dompet->id,
                            'santri_id' => $dompet->santri_id,
                            'santri_nama' => $dompet->santri->nama_santri,
                            'santri_nis' => $dompet->santri->nis,
                            'saldo' => $dompet->saldo,
                            'status' => $dompet->status,
                            'last_update' => $dompet->updated_at->format('Y-m-d H:i:s')
                        ];
                    });
        
        return response()->json([
            'success' => true,
            'data' => $dompets
        ]);
    }
    
    /**
     * Get dompet detail for a specific santri
     *
     * @param Request $request
     * @param int $id ID santri
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDompetDetail(Request $request, $id)
    {
        // Verify the santri belongs to the authenticated user
        $user = $request->user();
        $santri = Santri::where('id', $id)
                    ->where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('email_orangtua', $user->email);
                    })
                    ->first();
        
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan atau Anda tidak memiliki akses'
            ], 403);
        }
        
        // Get dompet info
        $dompet = Dompet::where('santri_id', $santri->id)
                    ->with('santri', 'limits')
                    ->first();
        
        if (!$dompet) {
            return response()->json([
                'success' => false,
                'message' => 'Dompet santri tidak ditemukan'
            ], 404);
        }
        
        // Get dompet limits
        $limits = $dompet->limits->map(function($limit) {
            return [
                'id' => $limit->id,
                'jenis' => $limit->jenis,
                'nominal' => $limit->nominal,
                'periode' => $limit->periode,
                'status' => $limit->status
            ];
        });
        
        // Prepare response data
        $data = [
            'id' => $dompet->id,
            'santri_id' => $dompet->santri_id,
            'santri_nama' => $dompet->santri->nama_santri,
            'santri_nis' => $dompet->santri->nis,
            'saldo' => $dompet->saldo,
            'status' => $dompet->status,
            'limits' => $limits,
            'last_update' => $dompet->updated_at->format('Y-m-d H:i:s')
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Get transactions for a specific dompet
     *
     * @param Request $request
     * @param int $id ID santri
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransaksiDompet(Request $request, $id)
    {
        // Verify the santri belongs to the authenticated user
        $user = $request->user();
        $santri = Santri::where('id', $id)
                    ->where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('email_orangtua', $user->email);
                    })
                    ->first();
        
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan atau Anda tidak memiliki akses'
            ], 403);
        }
        
        // Get dompet
        $dompet = Dompet::where('santri_id', $santri->id)->first();
        
        if (!$dompet) {
            return response()->json([
                'success' => false,
                'message' => 'Dompet santri tidak ditemukan'
            ], 404);
        }
        
        // Get transactions
        $transaksi = TransaksiDompet::where('dompet_id', $dompet->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
        
        // Transform data
        $transactions = $transaksi->map(function($t) {
            return [
                'id' => $t->id,
                'tanggal' => $t->created_at->format('Y-m-d H:i:s'),
                'jenis' => $t->jenis,
                'nominal' => $t->nominal,
                'deskripsi' => $t->keterangan,
                'saldo_sebelum' => $t->saldo_sebelum,
                'saldo_sesudah' => $t->saldo_sesudah,
                'status' => $t->status
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $transactions,
            'pagination' => [
                'current_page' => $transaksi->currentPage(),
                'last_page' => $transaksi->lastPage(),
                'per_page' => $transaksi->perPage(),
                'total' => $transaksi->total()
            ]
        ]);
    }
    
    /**
     * Update daily limit for santri's dompet
     * 
     * @param Request $request
     * @param int $id ID santri
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLimitHarian(Request $request, $id)
    {
        $request->validate([
            'limit_harian' => 'required|numeric|min:0',
        ]);
        
        // Verify the santri belongs to the authenticated user
        $user = $request->user();
        $santri = Santri::where('id', $id)
                    ->where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('email_orangtua', $user->email);
                    })
                    ->first();
        
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan atau Anda tidak memiliki akses'
            ], 403);
        }
        
        // Get dompet
        $dompet = Dompet::where('santri_id', $santri->id)->first();
        
        if (!$dompet) {
            return response()->json([
                'success' => false,
                'message' => 'Dompet santri tidak ditemukan'
            ], 404);
        }
        
        DB::beginTransaction();
        try {
            // Update atau create limit harian
            $limit = $dompet->limits()->updateOrCreate(
                [
                    'jenis' => 'harian',
                    'periode' => 'harian'
                ],
                [
                    'nominal' => $request->limit_harian,
                    'status' => 'aktif'
                ]
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Limit harian berhasil diperbarui',
                'data' => [
                    'id' => $limit->id,
                    'jenis' => $limit->jenis,
                    'nominal' => $limit->nominal,
                    'periode' => $limit->periode,
                    'status' => $limit->status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

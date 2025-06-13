<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Perizinan;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PerizinanController extends Controller
{
    /**
     * Get list of perizinan for the authenticated user's santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerizinanList(Request $request)
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

        // Check if Perizinan model exists
        if (!class_exists('App\\Models\\Perizinan')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur perizinan belum tersedia'
            ], 404);
        }

        // Get all perizinan for the santri
        try {
            $perizinan = Perizinan::whereIn('santri_id', $santris)
                ->with(['santri:id,nama_santri,nis'])
                ->orderBy('tanggal_mulai', 'desc')
                ->get()
                ->map(function($izin) {
                    return [
                        'id' => $izin->id,
                        'santri' => [
                            'id' => $izin->santri->id,
                            'nama' => $izin->santri->nama_santri,
                            'nis' => $izin->santri->nis,
                        ],
                        'jenis_izin' => $izin->jenis_izin,
                        'tanggal_mulai' => $izin->tanggal_mulai->format('Y-m-d'),
                        'tanggal_selesai' => $izin->tanggal_selesai->format('Y-m-d'),
                        'keterangan' => $izin->keterangan,
                        'status' => $izin->status,
                        'diajukan_pada' => $izin->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $perizinan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur perizinan belum tersedia: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a new perizinan request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPerizinan(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|integer',
            'jenis_izin' => 'required|string|in:sakit,pulang,keluar,kegiatan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if santri belongs to this user
        $santri = Santri::where('id', $request->santri_id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email_orangtua', $user->email);
            })
            ->first();
            
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan atau Anda tidak memiliki akses'
            ], 404);
        }
        
        // Check if Perizinan model exists
        if (!class_exists('App\\Models\\Perizinan')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur perizinan belum tersedia'
            ], 404);
        }
        
        try {
            // Handle file upload if provided
            $buktiPath = null;
            if ($request->hasFile('bukti')) {
                $buktiPath = $request->file('bukti')->store('perizinan', 'public');
            }
            
            // Create perizinan
            $perizinan = new Perizinan();
            $perizinan->santri_id = $santri->id;
            $perizinan->jenis_izin = $request->jenis_izin;
            $perizinan->tanggal_mulai = Carbon::parse($request->tanggal_mulai);
            $perizinan->tanggal_selesai = Carbon::parse($request->tanggal_selesai);
            $perizinan->keterangan = $request->keterangan;
            $perizinan->bukti = $buktiPath;
            $perizinan->status = 'menunggu'; // Default status
            $perizinan->created_by = $user->id;
            $perizinan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Perizinan berhasil diajukan',
                'data' => [
                    'id' => $perizinan->id,
                    'santri_id' => $perizinan->santri_id,
                    'jenis_izin' => $perizinan->jenis_izin,
                    'tanggal_mulai' => $perizinan->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $perizinan->tanggal_selesai->format('Y-m-d'),
                    'keterangan' => $perizinan->keterangan,
                    'status' => $perizinan->status,
                    'bukti' => $perizinan->bukti ? asset('storage/' . $perizinan->bukti) : null,
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat perizinan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get detailed information about a specific perizinan
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerizinanDetail(Request $request, $id)
    {
        $user = $request->user();
        
        // Get santri IDs associated with this user
        $santriIds = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->pluck('id');
            
        // Check if Perizinan model exists
        if (!class_exists('App\\Models\\Perizinan')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur perizinan belum tersedia'
            ], 404);
        }
        
        try {
            // Get perizinan detail with protection to ensure it belongs to user's santri
            $perizinan = Perizinan::where('id', $id)
                ->whereIn('santri_id', $santriIds)
                ->with(['santri:id,nama_santri,nis'])
                ->first();
                
            if (!$perizinan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perizinan tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            // Format the response
            $data = [
                'id' => $perizinan->id,
                'santri' => [
                    'id' => $perizinan->santri->id,
                    'nama' => $perizinan->santri->nama_santri,
                    'nis' => $perizinan->santri->nis,
                ],
                'jenis_izin' => $perizinan->jenis_izin,
                'tanggal_mulai' => $perizinan->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $perizinan->tanggal_selesai->format('Y-m-d'),
                'lama_izin' => $perizinan->tanggal_mulai->diffInDays($perizinan->tanggal_selesai) + 1,
                'keterangan' => $perizinan->keterangan,
                'status' => $perizinan->status,
                'alasan_ditolak' => $perizinan->alasan_ditolak,
                'bukti' => $perizinan->bukti ? asset('storage/' . $perizinan->bukti) : null,
                'diajukan_pada' => $perizinan->created_at->format('Y-m-d H:i:s'),
                'diperbarui_pada' => $perizinan->updated_at->format('Y-m-d H:i:s'),
                'disetujui_oleh' => $perizinan->approved_by,
                'disetujui_pada' => $perizinan->approved_at ? Carbon::parse($perizinan->approved_at)->format('Y-m-d H:i:s') : null,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail perizinan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update a perizinan request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePerizinan(Request $request, $id)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'jenis_izin' => 'string|in:sakit,pulang,keluar,kegiatan',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date|after_or_equal:tanggal_mulai',
            'keterangan' => 'string',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Get santri IDs associated with this user
        $santriIds = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->pluck('id');
        
        // Check if Perizinan model exists
        if (!class_exists('App\\Models\\Perizinan')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur perizinan belum tersedia'
            ], 404);
        }
        
        try {
            // Get perizinan with protection to ensure it belongs to user's santri
            $perizinan = Perizinan::where('id', $id)
                ->whereIn('santri_id', $santriIds)
                ->first();
                
            if (!$perizinan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perizinan tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            // Can only update if status is 'menunggu'
            if ($perizinan->status !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Perizinan tidak dapat diubah karena status sudah ' . $perizinan->status
                ], 400);
            }
            
            // Handle file upload if provided
            if ($request->hasFile('bukti')) {
                // Delete old file if exists
                if ($perizinan->bukti && \Storage::disk('public')->exists($perizinan->bukti)) {
                    \Storage::disk('public')->delete($perizinan->bukti);
                }
                
                $buktiPath = $request->file('bukti')->store('perizinan', 'public');
                $perizinan->bukti = $buktiPath;
            }
            
            // Update perizinan
            if ($request->has('jenis_izin')) {
                $perizinan->jenis_izin = $request->jenis_izin;
            }
            
            if ($request->has('tanggal_mulai')) {
                $perizinan->tanggal_mulai = Carbon::parse($request->tanggal_mulai);
            }
            
            if ($request->has('tanggal_selesai')) {
                $perizinan->tanggal_selesai = Carbon::parse($request->tanggal_selesai);
            }
            
            if ($request->has('keterangan')) {
                $perizinan->keterangan = $request->keterangan;
            }
            
            $perizinan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Perizinan berhasil diperbarui',
                'data' => [
                    'id' => $perizinan->id,
                    'jenis_izin' => $perizinan->jenis_izin,
                    'tanggal_mulai' => $perizinan->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $perizinan->tanggal_selesai->format('Y-m-d'),
                    'keterangan' => $perizinan->keterangan,
                    'status' => $perizinan->status,
                    'bukti' => $perizinan->bukti ? asset('storage/' . $perizinan->bukti) : null,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui perizinan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a perizinan request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePerizinan(Request $request, $id)
    {
        $user = $request->user();
        
        // Get santri IDs associated with this user
        $santriIds = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->pluck('id');
        
        // Check if Perizinan model exists
        if (!class_exists('App\\Models\\Perizinan')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur perizinan belum tersedia'
            ], 404);
        }
        
        try {
            // Get perizinan with protection to ensure it belongs to user's santri
            $perizinan = Perizinan::where('id', $id)
                ->whereIn('santri_id', $santriIds)
                ->first();
                
            if (!$perizinan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perizinan tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            // Can only delete if status is 'menunggu'
            if ($perizinan->status !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Perizinan tidak dapat dihapus karena status sudah ' . $perizinan->status
                ], 400);
            }
            
            // Delete bukti file if exists
            if ($perizinan->bukti && \Storage::disk('public')->exists($perizinan->bukti)) {
                \Storage::disk('public')->delete($perizinan->bukti);
            }
            
            // Delete perizinan
            $perizinan->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Perizinan berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus perizinan: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Dompet;
use App\Models\TransaksiDompet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DompetSantriController extends Controller
{
    /**
     * Get dompet (wallet) information for santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDompetInfo(Request $request)
    {
        // Get the authenticated user's santri
        $user = $request->user();
        $santris = Santri::where('user_id', $user->id)
                    ->orWhere('email_orangtua', $user->email)
                    ->pluck('id');
        
        // Get all dompets for these santris
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
                            'status' => $dompet->active ? 'Aktif' : 'Tidak Aktif',
                            'created_at' => $dompet->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $dompet->updated_at->format('Y-m-d H:i:s')
                        ];
                    });
        
        return response()->json([
            'success' => true,
            'data' => $dompets
        ]);
    }

    /**
     * Get dompet transactions for a santri
     *
     * @param Request $request
     * @param int $santriId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDompetTransaksi(Request $request, $santriId = null)
    {
        // Get the authenticated user's santri
        $user = $request->user();
        
        // If santriId is provided, check if this santri belongs to user
        if ($santriId) {
            $santri = Santri::where('id', $santriId)
                        ->where(function($query) use ($user) {
                            $query->where('user_id', $user->id)
                                  ->orWhere('email_orangtua', $user->email);
                        })
                        ->first();
            
            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke data santri ini'
                ], 403);
            }
            
            $santriIds = [$santriId];
        } else {
            // Get all santri IDs associated with user
            $santriIds = Santri::where('user_id', $user->id)
                        ->orWhere('email_orangtua', $user->email)
                        ->pluck('id')
                        ->toArray();
        }
        
        // Get all dompet IDs for these santris
        $dompetIds = Dompet::whereIn('santri_id', $santriIds)->pluck('id')->toArray();
        
        // Get transactions for these dompets
        $transaksi = TransaksiDompet::whereIn('dompet_id', $dompetIds)
                        ->with(['dompet.santri'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        $data = $transaksi->map(function($item) {
            return [
                'id' => $item->id,
                'dompet_id' => $item->dompet_id,
                'santri_nama' => $item->dompet->santri->nama_santri,
                'santri_nis' => $item->dompet->santri->nis,
                'jenis' => $item->jenis,
                'jumlah' => $item->jumlah,
                'keterangan' => $item->keterangan,
                'tanggal' => Carbon::parse($item->created_at)->format('Y-m-d H:i:s')
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $transaksi->currentPage(),
                'last_page' => $transaksi->lastPage(),
                'per_page' => $transaksi->perPage(),
                'total' => $transaksi->total()
            ]
        ]);
    }

    /**
     * Get dompet transaction summary (grouped by month)
     *
     * @param Request $request
     * @param int $santriId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDompetSummary(Request $request, $santriId = null)
    {
        // Get the authenticated user's santri
        $user = $request->user();
        
        // If santriId is provided, check if this santri belongs to user
        if ($santriId) {
            $santri = Santri::where('id', $santriId)
                        ->where(function($query) use ($user) {
                            $query->where('user_id', $user->id)
                                  ->orWhere('email_orangtua', $user->email);
                        })
                        ->first();
            
            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke data santri ini'
                ], 403);
            }
            
            $santriIds = [$santriId];
        } else {
            // Get all santri IDs associated with user
            $santriIds = Santri::where('user_id', $user->id)
                        ->orWhere('email_orangtua', $user->email)
                        ->pluck('id')
                        ->toArray();
        }
        
        // Get all dompet IDs for these santris
        $dompetIds = Dompet::whereIn('santri_id', $santriIds)->pluck('id')->toArray();
        
        // Get monthly summary for past 6 months
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        
        $summary = DB::table('transaksi_dompet')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(CASE WHEN jenis = "kredit" THEN jumlah ELSE 0 END) as total_kredit, SUM(CASE WHEN jenis = "debit" THEN jumlah ELSE 0 END) as total_debit')
            ->whereIn('dompet_id', $dompetIds)
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function($item) {
                $monthYear = Carbon::createFromFormat('Y-m', $item->month);
                return [
                    'bulan' => $monthYear->format('M Y'),
                    'total_kredit' => (float) $item->total_kredit,
                    'total_debit' => (float) $item->total_debit,
                    'net' => (float) $item->total_kredit - (float) $item->total_debit
                ];
            });
        
        // Get current total balance
        $totalSaldo = Dompet::whereIn('id', $dompetIds)->sum('saldo');
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_saldo' => $totalSaldo,
                'monthly_summary' => $summary
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Dompet;
use App\Models\DompetLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DompetLimitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya tampilkan dompet milik santri
        $query = Dompet::with(['santri.kelasRelasi', 'dompetLimit'])
                      ->where('jenis_pemilik', 'santri')
                      ->whereHas('santri');

        // Filter berdasarkan status aktif dompet
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        // Search berdasarkan nama santri atau NIS
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_dompet', 'like', "%{$search}%")
                  ->orWhereHas('santri', function($sq) use ($search) {
                      $sq->where('nama_santri', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        $dompets = $query->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('keuangan.dompet.set-limit.index', compact('dompets'));
    }

    /**
     * Update limit secara inline
     */
    public function updateLimit(Request $request, $dompetId)
    {
        $request->validate([
            'limit_harian' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $dompet = Dompet::findOrFail($dompetId);
            
            // Update atau create dompet limit hanya untuk limit harian
            DompetLimit::updateOrCreate(
                ['dompet_id' => $dompetId],
                [
                    'limit_harian' => $request->limit_harian,
                    'limit_transaksi' => $request->limit_harian, // Set sama dengan limit harian
                    'limit_mingguan' => $request->limit_harian * 7, // 7x limit harian
                    'limit_bulanan' => $request->limit_harian * 30, // 30x limit harian
                    'is_active' => true,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Limit harian berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil dompet yang belum ada limit-nya
        $dompets = Dompet::with(['santri', 'asatidz'])
            ->whereDoesntHave('dompetLimit')
            ->where('is_active', true)
            ->get();

        return view('dompet-limit.create', compact('dompets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'limit_harian' => 'required|numeric|min:0',
            'limit_transaksi' => 'required|numeric|min:0',
            'limit_mingguan' => 'nullable|numeric|min:0',
            'limit_bulanan' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'catatan' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah dompet sudah ada limit
            $existingLimit = DompetLimit::where('dompet_id', $request->dompet_id)->first();
            if ($existingLimit) {
                return back()->withErrors(['dompet_id' => 'Dompet ini sudah memiliki pengaturan limit.'])->withInput();
            }

            DompetLimit::create($request->all());

            DB::commit();

            return redirect()->route('dompet.set-limit.index')
                ->with('success', 'Pengaturan limit berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DompetLimit $dompetLimit)
    {
        $dompetLimit->load(['dompet.santri', 'dompet.asatidz']);
        return view('dompet-limit.show', compact('dompetLimit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DompetLimit $dompetLimit)
    {
        $dompetLimit->load(['dompet.santri', 'dompet.asatidz']);
        return view('dompet-limit.edit', compact('dompetLimit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DompetLimit $dompetLimit)
    {
        $request->validate([
            'limit_harian' => 'required|numeric|min:0',
            'limit_transaksi' => 'required|numeric|min:0',
            'limit_mingguan' => 'nullable|numeric|min:0',
            'limit_bulanan' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'catatan' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $dompetLimit->update($request->except('dompet_id'));

            DB::commit();

            return redirect()->route('dompet.set-limit.index')
                ->with('success', 'Pengaturan limit berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DompetLimit $dompetLimit)
    {
        try {
            DB::beginTransaction();

            $dompetLimit->delete();

            DB::commit();

            return redirect()->route('dompet.set-limit.index')
                ->with('success', 'Pengaturan limit berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle status aktif limit
     */
    public function toggleStatus(DompetLimit $dompetLimit)
    {
        try {
            $dompetLimit->update(['is_active' => !$dompetLimit->is_active]);
            
            $status = $dompetLimit->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return response()->json([
                'success' => true,
                'message' => "Limit berhasil {$status}.",
                'is_active' => $dompetLimit->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update limits for multiple dompets
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'dompet_ids' => 'required|array',
            'dompet_ids.*' => 'exists:dompets,id',
            'action' => 'required|in:set_limit,clear_limit,activate_all,deactivate_all',
            'limit_amount' => 'nullable|numeric|min:0'
        ]);

        $dompetIds = $request->dompet_ids;
        $action = $request->action;
        $limitAmount = $request->limit_amount;

        DB::beginTransaction();
        
        try {
            $successCount = 0;
            
            foreach ($dompetIds as $dompetId) {
                $dompet = Dompet::find($dompetId);
                
                if (!$dompet || $dompet->jenis_pemilik !== 'santri') {
                    continue;
                }

                switch ($action) {
                    case 'set_limit':
                        $dompetLimit = DompetLimit::updateOrCreate(
                            ['dompet_id' => $dompetId],
                            [
                                'limit_harian' => $limitAmount,
                                'is_active' => true
                            ]
                        );
                        $successCount++;
                        break;
                        
                    case 'clear_limit':
                        $dompetLimit = DompetLimit::updateOrCreate(
                            ['dompet_id' => $dompetId],
                            [
                                'limit_harian' => 0,
                                'is_active' => false
                            ]
                        );
                        $successCount++;
                        break;
                        
                    case 'activate_all':
                        $updated = DompetLimit::where('dompet_id', $dompetId)
                            ->where('limit_harian', '>', 0)
                            ->update([
                                'is_active' => true
                            ]);
                        if ($updated > 0) {
                            $successCount++;
                        }
                        break;
                        
                    case 'deactivate_all':
                        $updated = DompetLimit::where('dompet_id', $dompetId)
                            ->update([
                                'is_active' => false
                            ]);
                        if ($updated > 0) {
                            $successCount++;
                        }
                        break;
                }
            }

            DB::commit();

            $message = '';
            switch ($action) {
                case 'set_limit':
                    $message = "Berhasil mengatur limit harian Rp " . number_format($limitAmount, 0, ',', '.') . " untuk {$successCount} santri";
                    break;
                case 'clear_limit':
                    $message = "Berhasil menghapus limit harian untuk {$successCount} santri";
                    break;
                case 'activate_all':
                    $message = "Berhasil mengaktifkan limit untuk {$successCount} santri";
                    break;
                case 'deactivate_all':
                    $message = "Berhasil menonaktifkan limit untuk {$successCount} santri";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'processed_count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses bulk update: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\PembayaranSantri;
use App\Models\Transaksi;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class PembayaranSantriManagementController extends Controller
{
    /**
     * Display a listing of payment assignments
     */
    public function index(Request $request)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        
        $query = PembayaranSantri::with(['santri', 'jenisPembayaran', 'tahunAjaran'])
            ->activeYear()
            ->aktif();
            
        // Filter by jenis pembayaran
        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }
        
        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('santri.kelasAnggota', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }
        
        // Search by santri name
        if ($request->filled('search')) {
            $query->whereHas('santri', function($q) use ($request) {
                $q->where('nama_santri', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }
        
        $pembayaranSantris = $query->paginate(20);
        
        $jenisTagihans = JenisTagihan::activeYear()->get();
        $kelas = Kelas::all();
        
        return view('keuangan.pembayaran_santri_management.index', compact(
            'pembayaranSantris', 
            'jenisTagihans', 
            'kelas', 
            'activeTahunAjaran'
        ));
    }
    
    /**
     * Show form to assign payments to students
     */
    public function create(Request $request)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        $jenisTagihans = JenisTagihan::activeYear()->get();
        $kelas = Kelas::all();
        
        $selectedJenisPembayaran = null;
        $santris = collect();
        
        if ($request->filled('jenis_pembayaran_id')) {
            $selectedJenisPembayaran = JenisTagihan::find($request->jenis_pembayaran_id);
            
            if ($selectedJenisPembayaran) {
                // Get santris based on jenis pembayaran configuration
                if ($selectedJenisPembayaran->tipe_pembayaran === 'kelas') {
                    $kelasIds = $selectedJenisPembayaran->kelas_ids ?? [];
                    
                    if ($selectedJenisPembayaran->mode_santri === 'semua') {
                        // All students in selected classes
                        $santris = Santri::whereHas('kelasAnggota', function($q) use ($kelasIds) {
                            $q->whereIn('kelas_id', $kelasIds);
                        })->where('status', 'aktif')->get();
                    } else {
                        // Specific students
                        $santriIds = $selectedJenisPembayaran->santri_ids ?? [];
                        $santris = Santri::whereIn('id', $santriIds)
                            ->where('status', 'aktif')->get();
                    }
                } else {
                    // Manual - all active students
                    $santris = Santri::where('status', 'aktif')->get();
                }
                
                // Filter out students who already have this payment assigned
                $existingAssignments = PembayaranSantri::where('jenis_pembayaran_id', $selectedJenisPembayaran->id)
                    ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                    ->pluck('santri_id')
                    ->toArray();
                
                $santris = $santris->whereNotIn('id', $existingAssignments);
            }
        }
        
        return view('keuangan.pembayaran_santri_management.create', compact(
            'jenisTagihans',
            'kelas',
            'selectedJenisPembayaran',
            'santris',
            'activeTahunAjaran'
        ));
    }
    
    /**
     * Store payment assignments
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_tagihan_id' => 'required|exists:jenis_tagihans,id',
            'assignments' => 'required|array|min:1',
            'assignments.*.santri_id' => 'required|exists:santris,id',
            'assignments.*.nominal_tagihan' => 'required|numeric|min:0',
            'assignments.*.bulan_pembayaran' => 'nullable|array',
            'assignments.*.keterangan' => 'nullable|string|max:500'
        ]);
        
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif']);
        }
        
        $jenisTagihan = JenisTagihan::findOrFail($request->jenis_pembayaran_id);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->assignments as $assignment) {
                // Check if assignment already exists
                $existing = PembayaranSantri::where([
                    'santri_id' => $assignment['santri_id'],
                    'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                    'tahun_ajaran_id' => $activeTahunAjaran->id
                ])->first();
                
                if ($existing) {
                    continue; // Skip if already exists
                }
                
                PembayaranSantri::create([
                    'santri_id' => $assignment['santri_id'],
                    'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                    'tahun_ajaran_id' => $activeTahunAjaran->id,
                    'nominal_tagihan' => $assignment['nominal_tagihan'],
                    'bulan_pembayaran' => $assignment['bulan_pembayaran'] ?? $jenisTagihan->bulan_pembayaran,
                    'keterangan' => $assignment['keterangan'] ?? null,
                    'status' => 'aktif'
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('pembayaran-santri-management.index')
                ->with('success', 'Pembayaran berhasil ditetapkan untuk ' . count($request->assignments) . ' santri');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show edit form for specific payment assignment
     */
    public function edit(PembayaranSantri $pembayaranSantri)
    {
        $pembayaranSantri->load(['santri', 'jenisTagihan', 'tahunAjaran']);
        
        return view('keuangan.pembayaran_santri_management.edit', compact('pembayaranSantri'));
    }
    
    /**
     * Update payment assignment
     */
    public function update(Request $request, PembayaranSantri $pembayaranSantri)
    {
        $request->validate([
            'nominal_tagihan' => 'required|numeric|min:0',
            'bulan_pembayaran' => 'nullable|array',
            'status' => 'required|in:aktif,nonaktif',
            'keterangan' => 'nullable|string|max:500'
        ]);
        
        $pembayaranSantri->update([
            'nominal_tagihan' => $request->nominal_tagihan,
            'bulan_pembayaran' => $request->bulan_pembayaran,
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);
        
        return redirect()->route('pembayaran-santri-management.index')
            ->with('success', 'Data pembayaran berhasil diperbarui');
    }
    
    /**
     * Delete payment assignment
     */
    public function destroy(PembayaranSantri $pembayaranSantri)
    {
        try {
            // Check if there are any transactions
            if ($pembayaranSantri->transaksis()->count() > 0) {
                return back()->withErrors(['error' => 'Tidak dapat menghapus pembayaran yang sudah memiliki transaksi']);
            }
            
            $pembayaranSantri->delete();
            
            return redirect()->route('pembayaran-santri-management.index')
                ->with('success', 'Data pembayaran berhasil dihapus');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Bulk assign payments based on jenis pembayaran configuration
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'jenis_tagihan_id' => 'required|exists:jenis_tagihans,id',
            'use_default_nominal' => 'boolean'
        ]);
        
        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif']);
        }
        
        $jenisTagihan = JenisTagihan::findOrFail($request->jenis_pembayaran_id);
        
        // Get target students based on jenis pembayaran configuration
        $santris = collect();
        
        if ($jenisTagihan->tipe_pembayaran === 'kelas') {
            $kelasIds = $jenisTagihan->kelas_ids ?? [];
            
            if ($jenisTagihan->mode_santri === 'semua') {
                $santris = Santri::whereHas('kelasAnggota', function($q) use ($kelasIds) {
                    $q->whereIn('kelas_id', $kelasIds);
                })->where('status', 'aktif')->get();
            } else {
                $santriIds = $jenisTagihan->santri_ids ?? [];
                $santris = Santri::whereIn('id', $santriIds)
                    ->where('status', 'aktif')->get();
            }
        } else {
            return back()->withErrors(['error' => 'Bulk assign hanya tersedia untuk jenis pembayaran berbasis kelas']);
        }
        
        if ($santris->isEmpty()) {
            return back()->withErrors(['error' => 'Tidak ada santri yang ditemukan untuk jenis pembayaran ini']);
        }
        
        DB::beginTransaction();
        
        try {
            $created = 0;
            $nominal = $request->use_default_nominal ? $jenisTagihan->nominal : null;
            
            foreach ($santris as $santri) {
                // Check if assignment already exists
                $existing = PembayaranSantri::where([
                    'santri_id' => $santri->id,
                    'jenis_pembayaran_id' => $jenisTagihan->id,
                    'tahun_ajaran_id' => $activeTahunAjaran->id
                ])->first();
                
                if ($existing) {
                    continue; // Skip if already exists
                }
                
                PembayaranSantri::create([
                    'santri_id' => $santri->id,
                    'jenis_pembayaran_id' => $jenisTagihan->id,
                    'tahun_ajaran_id' => $activeTahunAjaran->id,
                    'nominal_tagihan' => $nominal ?? $jenisTagihan->nominal,
                    'bulan_pembayaran' => $jenisTagihan->bulan_pembayaran,
                    'status' => 'aktif'
                ]);
                
                $created++;
            }
            
            DB::commit();
            
            return redirect()->route('pembayaran-santri-management.index')
                ->with('success', "Berhasil menetapkan pembayaran untuk {$created} santri");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal melakukan bulk assign: ' . $e->getMessage()]);
        }
    }
}
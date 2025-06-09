<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKas;
use App\Models\BukuKas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TransaksiKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiKas::with(['bukuKas', 'bukuKasTujuan', 'creator', 'approver', 'tagihanSantri.santri']);
        
        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->jenis);
        }
        
        // Filter berdasarkan buku kas
        if ($request->filled('buku_kas_id')) {
            $query->where(function($q) use ($request) {
                $q->where('buku_kas_id', $request->buku_kas_id)
                  ->orWhere('buku_kas_tujuan_id', $request->buku_kas_id);
            });
        }
        
        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('tanggal_transaksi', [$request->dari_tanggal, $request->sampai_tanggal]);
        } elseif ($request->filled('dari_tanggal')) {
            $query->where('tanggal_transaksi', '>=', $request->dari_tanggal);
        } elseif ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_transaksi', '<=', $request->sampai_tanggal);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('no_referensi', 'like', "%{$search}%")
                  ->orWhereHas('bukuKas', function($q2) use ($search) {
                      $q2->where('nama_kas', 'like', "%{$search}%");
                  });
            });
        }
        
        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);
        
        // Data untuk dropdown filter
        $bukuKasList = BukuKas::where('is_active', true)
                             ->orderBy('nama_kas')
                             ->get();
                             
        // Statistik transaksi
        $stats = [
            'total_pemasukan' => TransaksiKas::where('jenis_transaksi', 'pemasukan')
                                            ->where('status', 'approved')
                                            ->sum('jumlah'),
            'total_pengeluaran' => TransaksiKas::where('jenis_transaksi', 'pengeluaran')
                                             ->where('status', 'approved')
                                             ->sum('jumlah'),
            'transaksi_pending' => TransaksiKas::where('status', 'pending')->count()
        ];
        
        // Memastikan bukuKasList tidak kosong
        if ($bukuKasList->isEmpty()) {
            $bukuKasList = collect([]); // Set empty collection jika kosong
        }

        return view('keuangan.transaksi-kas.index', compact('transaksi', 'bukuKasList', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get();
        $jenis = request('jenis') ?? 'pemasukan';
        
        return view('keuangan.transaksi-kas.create', compact('bukuKasList', 'jenis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug nilai yang diterima
        \Log::info('Request data:', $request->all());
        
        // Validasi dasar
        $rules = [
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran,transfer',
            'kategori' => 'required|string|max:100',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|string|max:100',
            'keterangan' => 'nullable|string'
        ];
        
        // Validasi khusus berdasarkan jenis transaksi
        if ($request->jenis_transaksi === 'transfer') {
            $rules['buku_kas_tujuan_id'] = 'required|exists:buku_kas,id|different:buku_kas_id';
        }
        
        // Validasi untuk file bukti
        if ($request->hasFile('bukti_transaksi')) {
            $rules['bukti_transaksi'] = 'file|mimes:jpeg,png,jpg,pdf|max:2048';
        }
        
        $validated = $request->validate($rules);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Generate kode transaksi
            $kodeTransaksi = TransaksiKas::generateKodeTransaksi($request->jenis_transaksi);
            
            // Upload bukti transaksi jika ada
            $buktiPath = null;
            if ($request->hasFile('bukti_transaksi')) {
                $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
            }
            
            // Buat transaksi
            $transaksi = TransaksiKas::create([
                'buku_kas_id' => $validated['buku_kas_id'],
                'buku_kas_tujuan_id' => $request->jenis_transaksi === 'transfer' ? $validated['buku_kas_tujuan_id'] : null,
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'kategori' => $validated['kategori'],
                'kode_transaksi' => $kodeTransaksi,
                'jumlah' => $validated['jumlah'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nama_pemohon' => $request->nama_pemohon,
                'no_referensi' => $request->no_referensi,
                'keterangan' => $validated['keterangan'],
                'bukti_transaksi' => $buktiPath,
                'status' => 'approved',
                'created_by' => Auth::id(),
                'approved_by' => Auth::id() // Auto-approve untuk saat ini
            ]);
            
            // Update saldo buku kas
            $bukuKas = BukuKas::findOrFail($validated['buku_kas_id']);
            
            if ($request->jenis_transaksi === 'pemasukan') {
                $bukuKas->updateSaldo($validated['jumlah'], 'masuk');
            } elseif ($request->jenis_transaksi === 'pengeluaran') {
                $bukuKas->updateSaldo($validated['jumlah'], 'keluar');
            } elseif ($request->jenis_transaksi === 'transfer') {
                // Kurangi saldo dari kas sumber
                $bukuKas->updateSaldo($validated['jumlah'], 'keluar');
                
                // Tambah saldo ke kas tujuan
                $bukuKasTujuan = BukuKas::findOrFail($validated['buku_kas_tujuan_id']);
                $bukuKasTujuan->updateSaldo($validated['jumlah'], 'masuk');
            }
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan',
                    'data' => $transaksi
                ]);
            }
            
            return redirect()->route('keuangan.transaksi-kas.index')
                ->with('success', 'Transaksi berhasil disimpan');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = TransaksiKas::with(['bukuKas', 'bukuKasTujuan', 'creator', 'approver', 'tagihanSantri.santri'])
                               ->findOrFail($id);
                               
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $transaksi
            ]);
        }
                               
        return view('keuangan.transaksi-kas.show', compact('transaksi'));
    }

    /**
     * API endpoint to get transaksi data for editing
     */
    public function apiShow($id)
    {
        try {
            $transaksi = TransaksiKas::with(['bukuKas', 'bukuKasTujuan'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $transaksi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaksi = TransaksiKas::findOrFail($id);
        $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get();
        
        // Hanya transaksi dengan status pending yang bisa diedit
        if ($transaksi->status !== 'pending' && !auth()->user()->hasRole('admin')) {
            return redirect()->route('keuangan.transaksi-kas.show', $id)
                ->with('error', 'Hanya transaksi dengan status pending yang dapat diubah');
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $transaksi,
                'bukuKasList' => $bukuKasList
            ]);
        }
        
        return view('keuangan.transaksi-kas.edit', compact('transaksi', 'bukuKasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = TransaksiKas::findOrFail($id);
        
        // Hanya transaksi dengan status pending yang bisa diupdate
        if ($transaksi->status !== 'pending' && !auth()->user()->hasRole('admin')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya transaksi dengan status pending yang dapat diubah'
                ], 403);
            }
            
            return redirect()->route('keuangan.transaksi-kas.show', $id)
                ->with('error', 'Hanya transaksi dengan status pending yang dapat diubah');
        }
        
        // Validasi dasar
        $rules = [
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'metode_pembayaran' => 'required|string|max:100',
            'no_referensi' => 'nullable|string|max:100',
        ];
        
        // Validasi untuk file bukti
        if ($request->hasFile('bukti_transaksi')) {
            $rules['bukti_transaksi'] = 'file|mimes:jpeg,png,jpg,pdf|max:2048';
        }
        
        $validated = $request->validate($rules);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Upload bukti transaksi jika ada
            if ($request->hasFile('bukti_transaksi')) {
                // Hapus file lama jika ada
                if ($transaksi->bukti_transaksi) {
                    Storage::disk('public')->delete($transaksi->bukti_transaksi);
                }
                
                $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
                $transaksi->bukti_transaksi = $buktiPath;
            }
            
            // Update data
            $transaksi->kategori = $validated['kategori'];
            $transaksi->keterangan = $validated['keterangan'];
            $transaksi->metode_pembayaran = $validated['metode_pembayaran'];
            $transaksi->no_referensi = $request->no_referensi;
            $transaksi->nama_pemohon = $request->nama_pemohon;
            
            // Handle amount from modal form if provided
            if ($request->filled('jumlah_raw')) {
                $transaksi->jumlah = $request->jumlah_raw;
            }
            
            // Update tanggal transaksi if provided and still pending
            if ($transaksi->status === 'pending' && $request->filled('tanggal_transaksi')) {
                $transaksi->tanggal_transaksi = $request->tanggal_transaksi;
            }
            
            // Update buku kas if provided and still pending
            if ($transaksi->status === 'pending' && $request->filled('buku_kas_id')) {
                $transaksi->buku_kas_id = $request->buku_kas_id;
            }
            
            $transaksi->save();
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil diperbarui',
                    'data' => $transaksi
                ]);
            }
            
            return redirect()->route('keuangan.transaksi-kas.index')
                ->with('success', 'Transaksi berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui transaksi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = TransaksiKas::findOrFail($id);
        
        // Hanya transaksi dengan status pending yang bisa dihapus
        if ($transaksi->status !== 'pending' && !auth()->user()->hasRole('admin')) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya transaksi dengan status pending yang dapat dihapus'
                ], 403);
            }
            
            return redirect()->route('keuangan.transaksi-kas.index')
                ->with('error', 'Hanya transaksi dengan status pending yang dapat dihapus');
        }
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Hapus file bukti jika ada
            if ($transaksi->bukti_transaksi) {
                Storage::disk('public')->delete($transaksi->bukti_transaksi);
            }
            
            $transaksi->delete();
            
            DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil dihapus'
                ]);
            }
            
            return redirect()->route('keuangan.transaksi-kas.index')
                ->with('success', 'Transaksi berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('keuangan.transaksi-kas.index')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
    
    /**
     * Approve a transaction
     */
    public function approve(string $id)
    {
        $transaksi = TransaksiKas::findOrFail($id);
        
        if ($transaksi->status !== 'pending') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ini tidak dalam status pending'
                ], 400);
            }
            
            return redirect()->route('keuangan.transaksi-kas.show', $id)
                ->with('error', 'Transaksi ini tidak dalam status pending');
        }
        
        DB::beginTransaction();
        
        try {
            // Update status transaksi
            $transaksi->status = 'approved';
            $transaksi->approved_by = Auth::id();
            $transaksi->save();
            
            // Update saldo buku kas
            $bukuKas = BukuKas::findOrFail($transaksi->buku_kas_id);
            
            if ($transaksi->jenis_transaksi === 'pemasukan') {
                $bukuKas->updateSaldo($transaksi->jumlah, 'masuk');
            } elseif ($transaksi->jenis_transaksi === 'pengeluaran') {
                $bukuKas->updateSaldo($transaksi->jumlah, 'keluar');
            } elseif ($transaksi->jenis_transaksi === 'transfer') {
                // Kurangi saldo dari kas sumber
                $bukuKas->updateSaldo($transaksi->jumlah, 'keluar');
                
                // Tambah saldo ke kas tujuan
                $bukuKasTujuan = BukuKas::findOrFail($transaksi->buku_kas_tujuan_id);
                $bukuKasTujuan->updateSaldo($transaksi->jumlah, 'masuk');
            }
            
            DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil disetujui',
                    'data' => $transaksi
                ]);
            }
            
            return redirect()->route('keuangan.transaksi-kas.show', $id)
                ->with('success', 'Transaksi berhasil disetujui');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui transaksi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menyetujui transaksi: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a transaction
     */
    public function reject(Request $request, string $id)
    {
        $transaksi = TransaksiKas::findOrFail($id);
        
        if ($transaksi->status !== 'pending') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ini tidak dalam status pending'
                ], 400);
            }
            
            return redirect()->route('keuangan.transaksi-kas.show', $id)
                ->with('error', 'Transaksi ini tidak dalam status pending');
        }
        
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update status transaksi
            $transaksi->status = 'rejected';
            $transaksi->keterangan = $transaksi->keterangan . "\n[DITOLAK]: " . $request->alasan_penolakan;
            $transaksi->save();
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil ditolak',
                    'data' => $transaksi
                ]);
            }
            
            return redirect()->route('keuangan.transaksi-kas.show', $id)
                ->with('success', 'Transaksi berhasil ditolak');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak transaksi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }
}

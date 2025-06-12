<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Jabatan::with(['creator', 'bidang'])->withCount('pegawais')->orderByLevel();

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_jabatan', $request->kategori);
        }

        // Filter berdasarkan bidang
        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('bidang_id', $request->bidang);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_jabatan', 'like', "%{$search}%")
                  ->orWhere('kode_jabatan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $jabatans = $query->paginate(15)->withQueryString();

        // Statistik
        $stats = [
            'total' => Jabatan::count(),
            'aktif' => Jabatan::where('status', 'aktif')->count(),
            'nonaktif' => Jabatan::where('status', 'nonaktif')->count(),
            'total_pegawai' => Pegawai::whereHas('jabatan')->count(),
        ];

        // Data untuk filter
        $bidangs = Bidang::aktif()->ordered()->get();
        $kategoris = [
            'pengasuh' => 'Dewan Pengasuh',
            'pengurus' => 'Pengurus Yayasan', 
            'pimpinan' => 'Pimpinan Pesantren',
            'naib' => 'Wakil Pimpinan',
            'kepala' => 'Kepala Unit/Bagian',
            'staff' => 'Staff/Pelaksana'
        ];

        return view('kepegawaian.jabatan.index', compact('jabatans', 'stats', 'bidangs', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bidangs = Bidang::aktif()->ordered()->get();
        $kategoris = [
            'pengasuh' => 'Dewan Pengasuh',
            'pengurus' => 'Pengurus Yayasan', 
            'pimpinan' => 'Pimpinan Pesantren',
            'naib' => 'Wakil Pimpinan',
            'kepala' => 'Kepala Unit/Bagian',
            'staff' => 'Staff/Pelaksana'
        ];
        $parentJabatans = Jabatan::aktif()->orderByLevel()->get();
        
        return view('kepegawaian.jabatan.create', compact('bidangs', 'kategoris', 'parentJabatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan',
            'kode_jabatan' => 'required|string|max:10|unique:jabatans,kode_jabatan',
            'kategori_jabatan' => 'required|in:pengasuh,pengurus,pimpinan,naib,kepala,staff',
            'bidang_id' => 'nullable|exists:bidangs,id',
            'parent_jabatan_id' => 'nullable|exists:jabatans,id',
            'deskripsi' => 'nullable|string',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
            'level_jabatan' => 'required|integer|min:1|max:6',
            'status' => 'required|in:aktif,nonaktif',
            'is_struktural' => 'boolean'
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi',
            'nama_jabatan.unique' => 'Nama jabatan sudah ada',
            'kode_jabatan.required' => 'Kode jabatan wajib diisi',
            'kode_jabatan.unique' => 'Kode jabatan sudah ada',
            'kategori_jabatan.required' => 'Kategori jabatan wajib dipilih',
            'bidang_id.exists' => 'Bidang yang dipilih tidak valid',
            'parent_jabatan_id.exists' => 'Parent jabatan yang dipilih tidak valid',
            'gaji_pokok.required' => 'Gaji pokok wajib diisi',
            'gaji_pokok.numeric' => 'Gaji pokok harus berupa angka',
            'level_jabatan.required' => 'Level jabatan wajib diisi',
            'level_jabatan.min' => 'Level jabatan minimal 1',
            'level_jabatan.max' => 'Level jabatan maksimal 6'
        ]);

        try {
            DB::beginTransaction();

            Jabatan::create([
                'nama_jabatan' => $request->nama_jabatan,
                'kode_jabatan' => strtoupper($request->kode_jabatan),
                'kategori_jabatan' => $request->kategori_jabatan,
                'bidang_id' => $request->bidang_id,
                'parent_jabatan_id' => $request->parent_jabatan_id,
                'deskripsi' => $request->deskripsi,
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->tunjangan ?? 0,
                'level_jabatan' => $request->level_jabatan,
                'status' => $request->status,
                'is_struktural' => $request->has('is_struktural'),
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('kepegawaian.jabatan.index')
                ->with('success', 'Jabatan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal menambahkan jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jabatan $jabatan)
    {
        $jabatan->load(['creator', 'pegawais']);
        
        // Statistik pegawai dengan jabatan ini
        $pegawai_stats = [
            'total' => $jabatan->pegawais->count(),
            'aktif' => $jabatan->pegawais->where('status_pegawai', 'Aktif')->count(),
            'nonaktif' => $jabatan->pegawais->where('status_pegawai', '!=', 'Aktif')->count(),
        ];

        return view('kepegawaian.jabatan.show', compact('jabatan', 'pegawai_stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        $bidangs = Bidang::aktif()->ordered()->get();
        $kategoris = [
            'pengasuh' => 'Dewan Pengasuh',
            'pengurus' => 'Pengurus Yayasan', 
            'pimpinan' => 'Pimpinan Pesantren',
            'naib' => 'Wakil Pimpinan',
            'kepala' => 'Kepala Unit/Bagian',
            'staff' => 'Staff/Pelaksana'
        ];
        $parentJabatans = Jabatan::aktif()->where('id', '!=', $jabatan->id)->orderByLevel()->get();
        
        return view('kepegawaian.jabatan.edit', compact('jabatan', 'bidangs', 'kategoris', 'parentJabatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan,' . $jabatan->id,
            'kode_jabatan' => 'required|string|max:10|unique:jabatans,kode_jabatan,' . $jabatan->id,
            'kategori_jabatan' => 'required|in:pengasuh,pengurus,pimpinan,naib,kepala,staff',
            'bidang_id' => 'nullable|exists:bidangs,id',
            'parent_jabatan_id' => 'nullable|exists:jabatans,id|not_in:' . $jabatan->id,
            'deskripsi' => 'nullable|string',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
            'level_jabatan' => 'required|integer|min:1|max:6',
            'status' => 'required|in:aktif,nonaktif',
            'is_struktural' => 'boolean'
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi',
            'nama_jabatan.unique' => 'Nama jabatan sudah ada',
            'kode_jabatan.required' => 'Kode jabatan wajib diisi',
            'kode_jabatan.unique' => 'Kode jabatan sudah ada',
            'kategori_jabatan.required' => 'Kategori jabatan wajib dipilih',
            'bidang_id.exists' => 'Bidang yang dipilih tidak valid',
            'parent_jabatan_id.exists' => 'Parent jabatan yang dipilih tidak valid',
            'parent_jabatan_id.not_in' => 'Jabatan tidak boleh menjadi parent dari dirinya sendiri',
            'gaji_pokok.required' => 'Gaji pokok wajib diisi',
            'gaji_pokok.numeric' => 'Gaji pokok harus berupa angka',
            'level_jabatan.required' => 'Level jabatan wajib diisi'
        ]);

        try {
            DB::beginTransaction();

            $jabatan->update([
                'nama_jabatan' => $request->nama_jabatan,
                'kode_jabatan' => strtoupper($request->kode_jabatan),
                'kategori_jabatan' => $request->kategori_jabatan,
                'bidang_id' => $request->bidang_id,
                'parent_jabatan_id' => $request->parent_jabatan_id,
                'deskripsi' => $request->deskripsi,
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->tunjangan ?? 0,
                'level_jabatan' => $request->level_jabatan,
                'status' => $request->status,
                'is_struktural' => $request->has('is_struktural')
            ]);

            DB::commit();

            return redirect()->route('kepegawaian.jabatan.show', $jabatan)
                ->with('success', 'Jabatan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        try {
            DB::beginTransaction();

            // Cek apakah jabatan masih digunakan oleh pegawai
            if ($jabatan->pegawais()->count() > 0) {
                return back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh ' . $jabatan->pegawais()->count() . ' pegawai');
            }

            $jabatan->delete();

            DB::commit();

            return redirect()->route('kepegawaian.jabatan.index')
                ->with('success', 'Jabatan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status jabatan
     */
    public function toggleStatus(Jabatan $jabatan)
    {
        try {
            $newStatus = $jabatan->status === 'aktif' ? 'nonaktif' : 'aktif';
            
            $jabatan->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status jabatan berhasil diubah menjadi ' . $newStatus,
                'status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('akademik.tahun_ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('akademik.tahun_ajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:255',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Jika tahun ajaran baru diset sebagai aktif, nonaktifkan yang lain
            if ($request->has('is_active') && $request->is_active) {
                TahunAjaran::query()->update(['is_active' => false]);
            }

            TahunAjaran::create($validated);

            DB::commit();
            return redirect()->route('akademik.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tahun ajaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAjaran $tahunAjaran)
    {
        return view('akademik.tahun_ajaran.show', compact('tahunAjaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('akademik.tahun_ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:255',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Jika tahun ajaran diset sebagai aktif, nonaktifkan yang lain
            if ($request->has('is_active') && $request->is_active) {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)
                    ->update(['is_active' => false]);
            }

            $tahunAjaran->update($validated);

            DB::commit();
            return redirect()->route('akademik.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui tahun ajaran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            // Cek apakah tahun ajaran sedang aktif
            if ($tahunAjaran->is_active) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
            }

            $tahunAjaran->delete();
            return redirect()->route('akademik.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus tahun ajaran: ' . $e->getMessage());
        }
    }

    /**
     * Set tahun ajaran sebagai aktif
     */
    public function activate(TahunAjaran $tahunAjaran)
    {
        DB::beginTransaction();
        try {
            // Nonaktifkan semua tahun ajaran
            TahunAjaran::query()->update(['is_active' => false]);
            
            // Aktifkan tahun ajaran yang dipilih
            $tahunAjaran->update(['is_active' => true]);

            DB::commit();
            return redirect()->route('akademik.tahun-ajaran.index')
                ->with('success', 'Status tahun ajaran berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan tahun ajaran: ' . $e->getMessage());
        }
    }
}
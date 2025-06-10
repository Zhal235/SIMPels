<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pegawai', $request->status);
        }

        // Filter by jabatan
        if ($request->has('jabatan') && $request->jabatan != '') {
            $query->where('jabatan', $request->jabatan);
        }

        // Filter by divisi
        if ($request->has('divisi') && $request->divisi != '') {
            $query->where('divisi', $request->divisi);
        }

        $pegawais = $query->orderBy('nama_pegawai', 'asc')->paginate(20);

        // Get unique jabatan and divisi for filter options
        $jabatanOptions = Pegawai::distinct()->pluck('jabatan')->filter()->sort()->values();
        $divisiOptions = Pegawai::distinct()->pluck('divisi')->filter()->sort()->values();

        return view('kepegawaian.pegawai.index', compact('pegawais', 'jabatanOptions', 'divisiOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kepegawaian.pegawai.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'nik' => 'required|string|max:16|unique:pegawais,nik',
            'nip' => 'nullable|string|max:20|unique:pegawais,nip',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:pegawais,email',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati',
            'pendidikan_terakhir' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'institusi' => 'nullable|string|max:255',
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . date('Y'),
            'jabatan' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'status_pegawai' => 'required|in:Aktif,Non-Aktif,Pensiun,Resign',
            'jenis_pegawai' => 'required|in:Tetap,Kontrak,Honorer,Magang',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['foto']);

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $filename = time() . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('pegawai', $filename, 'public');
                $data['foto'] = $path;
            }

            Pegawai::create($data);

            DB::commit();

            return redirect()->route('kepegawaian.pegawai.index')
                ->with('success', 'Data pegawai berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.show', compact('pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.edit', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'nik' => 'required|string|max:16|unique:pegawais,nik,' . $pegawai->id,
            'nip' => 'nullable|string|max:20|unique:pegawais,nip,' . $pegawai->id,
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:pegawais,email,' . $pegawai->id,
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati',
            'pendidikan_terakhir' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'institusi' => 'nullable|string|max:255',
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . date('Y'),
            'jabatan' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after:tanggal_masuk',
            'status_pegawai' => 'required|in:Aktif,Non-Aktif,Pensiun,Resign',
            'jenis_pegawai' => 'required|in:Tetap,Kontrak,Honorer,Magang',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['foto']);

            // Handle foto upload
            if ($request->hasFile('foto')) {
                // Delete old foto if exists
                if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                    Storage::disk('public')->delete($pegawai->foto);
                }

                $foto = $request->file('foto');
                $filename = time() . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('pegawai', $filename, 'public');
                $data['foto'] = $path;
            }

            $pegawai->update($data);

            DB::commit();

            return redirect()->route('kepegawaian.pegawai.index')
                ->with('success', 'Data pegawai berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        try {
            DB::beginTransaction();

            // Delete foto if exists
            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $pegawai->delete();

            DB::commit();

            return redirect()->route('kepegawaian.pegawai.index')
                ->with('success', 'Data pegawai berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}

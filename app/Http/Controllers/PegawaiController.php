<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Bidang;
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
        $query = Pegawai::with(['jabatan', 'jabatan.bidang']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhereHas('jabatan', function($subQ) use ($search) {
                      $subQ->where('nama_jabatan', 'like', "%{$search}%")
                           ->orWhere('kode_jabatan', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pegawai', $request->status);
        }

        // Filter by jabatan_id
        if ($request->has('jabatan_id') && $request->jabatan_id != '') {
            $query->where('jabatan_id', $request->jabatan_id);
        }

        // Filter by bidang
        if ($request->has('bidang_id') && $request->bidang_id != '') {
            $query->where(function($q) use ($request) {
                $q->whereHas('jabatan', function($subQ) use ($request) {
                    $subQ->where('bidang_id', $request->bidang_id);
                })
                ->orWhereHas('jabatansAktif', function($subQ) use ($request) {
                    $subQ->where('bidang_id', $request->bidang_id);
                });
            });
        }

        $pegawais = $query->orderBy('nama_pegawai', 'asc')->paginate(20)->withQueryString();

        // Eager load relasi
        $pegawais->load([
            'jabatan.bidang', 
            'jabatansAktif.bidang'
        ]);
        
        // Get options for filter dropdowns
        $jabatanOptions = Jabatan::aktif()->orderByLevel()->get();
        $bidangOptions = Bidang::aktif()->ordered()->get();

        return view('kepegawaian.pegawai.index', compact('pegawais', 'jabatanOptions', 'bidangOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jabatanOptions = Jabatan::aktif()->orderByLevel()->get();
        $bidangOptions = Bidang::aktif()->ordered()->get();
        
        return view('kepegawaian.pegawai.create', compact('jabatanOptions', 'bidangOptions'));
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
            'jabatan_utama_id' => 'required|exists:jabatans,id',
            'tanggal_masuk' => 'required|date',
            'status_pegawai' => 'required|in:Aktif,Non-Aktif,Pensiun,Resign',
            'jenis_pegawai' => 'required|in:Tetap,Kontrak,Honorer,Magang',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'jabatan_utama_id.required' => 'Jabatan utama wajib dipilih',
            'jabatan_utama_id.exists' => 'Jabatan yang dipilih tidak valid'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['foto', 'jabatan_utama_id', 'jabatan_tambahan']);
            
            // Auto-fill jabatan string dari jabatan_utama_id
            $jabatanUtama = null;
            if ($request->jabatan_utama_id) {
                $jabatanUtama = Jabatan::find($request->jabatan_utama_id);
                if ($jabatanUtama) {
                    // Set jabatan_id untuk backward compatibility
                    $data['jabatan_id'] = $jabatanUtama->id;
                    $data['jabatan'] = $jabatanUtama->nama_jabatan;
                    $data['divisi'] = $jabatanUtama->bidang ? $jabatanUtama->bidang->nama_bidang : null;
                    
                    // Auto-set gaji pokok dari jabatan jika belum diisi
                    if (!$request->gaji_pokok && $jabatanUtama->gaji_pokok > 0) {
                        $data['gaji_pokok'] = $jabatanUtama->gaji_pokok;
                    }
                }
            }

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $filename = time() . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('pegawai', $filename, 'public');
                $data['foto'] = $path;
            }

            // Buat pegawai dengan data dasar
            $pegawai = Pegawai::create($data);

            // Tambahkan jabatan utama ke tabel pivot
            if ($jabatanUtama) {
                $pegawai->pegawaiJabatans()->create([
                    'jabatan_id' => $jabatanUtama->id,
                    'is_jabatan_utama' => true,
                    'tanggal_mulai' => now()->format('Y-m-d'),
                    'status' => 'aktif'
                ]);
            }

            // Tambahkan jabatan tambahan ke tabel pivot jika ada
            if ($request->has('jabatan_tambahan') && is_array($request->jabatan_tambahan)) {
                foreach ($request->jabatan_tambahan as $jabatanId) {
                    if ($jabatanId != $request->jabatan_utama_id) {
                        $pegawai->pegawaiJabatans()->create([
                            'jabatan_id' => $jabatanId,
                            'is_jabatan_utama' => false,
                            'tanggal_mulai' => now()->format('Y-m-d'),
                            'status' => 'aktif'
                        ]);
                    }
                }
            }

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
        $jabatanOptions = Jabatan::aktif()->orderByLevel()->get();
        $bidangOptions = Bidang::aktif()->ordered()->get();
        
        // Dapatkan jabatan tambahan IDs untuk select form
        $pegawai->jabatan_tambahan_ids = $pegawai->pegawaiJabatans()
            ->where('is_jabatan_utama', false)
            ->where('status', 'aktif')
            ->pluck('jabatan_id')
            ->toArray();
        
        return view('kepegawaian.pegawai.edit', compact('pegawai', 'jabatanOptions', 'bidangOptions'));
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
            'jabatan_utama_id' => 'required|exists:jabatans,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after:tanggal_masuk',
            'status_pegawai' => 'required|in:Aktif,Non-Aktif,Pensiun,Resign',
            'jenis_pegawai' => 'required|in:Tetap,Kontrak,Honorer,Magang',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'jabatan_utama_id.required' => 'Jabatan utama wajib dipilih',
            'jabatan_utama_id.exists' => 'Jabatan yang dipilih tidak valid'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['foto', 'jabatan_utama_id', 'jabatan_tambahan']);
            
            // Auto-fill jabatan string dari jabatan_utama_id
            $jabatanUtama = null;
            if ($request->jabatan_utama_id) {
                $jabatanUtama = Jabatan::find($request->jabatan_utama_id);
                if ($jabatanUtama) {
                    // Set jabatan_id untuk backward compatibility
                    $data['jabatan_id'] = $jabatanUtama->id;
                    $data['jabatan'] = $jabatanUtama->nama_jabatan;
                    $data['divisi'] = $jabatanUtama->bidang ? $jabatanUtama->bidang->nama_bidang : null;
                }
            }

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

            // Update pegawai data
            $pegawai->update($data);
            
            // Set semua jabatan saat ini menjadi non-aktif
            $pegawai->pegawaiJabatans()->update([
                'is_jabatan_utama' => false,
                'status' => 'nonaktif',
                'tanggal_selesai' => now()->format('Y-m-d')
            ]);

            // Tambahkan jabatan utama baru
            if ($jabatanUtama) {
                // Cek apakah sudah ada record jabatan ini, jika ya update statusnya
                $existingJabatan = $pegawai->pegawaiJabatans()
                    ->where('jabatan_id', $jabatanUtama->id)
                    ->first();
                    
                if ($existingJabatan) {                        $existingJabatan->update([
                            'is_jabatan_utama' => true,
                            'tanggal_mulai' => now()->format('Y-m-d'),
                            'tanggal_selesai' => null,
                            'status' => 'aktif'
                        ]);
                } else {
                    $pegawai->pegawaiJabatans()->create([
                        'jabatan_id' => $jabatanUtama->id,
                        'is_jabatan_utama' => true,
                        'tanggal_mulai' => now()->format('Y-m-d'),
                        'status' => 'aktif'
                    ]);
                }
            }

            // Tambahkan jabatan tambahan jika ada
            if ($request->has('jabatan_tambahan') && is_array($request->jabatan_tambahan)) {
                foreach ($request->jabatan_tambahan as $jabatanId) {
                    if ($jabatanId != $request->jabatan_utama_id) {
                        // Cek apakah sudah ada record jabatan ini
                        $existingJabatan = $pegawai->pegawaiJabatans()
                            ->where('jabatan_id', $jabatanId)
                            ->first();
                            
                        if ($existingJabatan) {                        $existingJabatan->update([
                            'is_jabatan_utama' => false,
                            'tanggal_mulai' => now()->format('Y-m-d'),
                            'tanggal_selesai' => null,
                            'status' => 'aktif'
                        ]);
                        } else {
                            $pegawai->pegawaiJabatans()->create([
                                'jabatan_id' => $jabatanId,
                                'is_jabatan_utama' => false,
                                'tanggal_mulai' => now()->format('Y-m-d'),
                                'status' => 'aktif'
                            ]);
                        }
                    }
                }
            }

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

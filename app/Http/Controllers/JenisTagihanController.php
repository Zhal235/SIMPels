<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\JenisTagihanKelas;
use Illuminate\Http\Request;

class JenisTagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisTagihans = JenisTagihan::with('tahunAjaran')->paginate(10);
        $activeTahunAjaran = TahunAjaran::getActive();
        return view('keuangan.jenis_tagihan.index', compact('jenisTagihans', 'activeTahunAjaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        return view('keuangan.jenis_tagihan.create', compact('tahunAjarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'nominal' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ];

        // Validasi kondisional berdasarkan jenis pembayaran
        if ($request->jenis_pembayaran === 'rutin') {
            $rules['is_bulanan'] = 'required|boolean';
            if ($request->is_bulanan) {
                $rules['bulan_pembayaran'] = 'required|array|min:1';
                $rules['bulan_pembayaran.*'] = 'integer|between:1,12';
            }
        } elseif ($request->jenis_pembayaran === 'insidentil') {
            $rules['tahun_ajaran_id'] = 'required|exists:tahun_ajarans,id';
            $rules['bulan_pembayaran'] = 'required|array|min:1';
            $rules['bulan_pembayaran.*'] = 'integer|between:1,12';
        }

        $validated = $request->validate($rules);

        // Set default values berdasarkan jenis pembayaran
        if ($request->jenis_pembayaran === 'rutin') {
            $validated['tahun_ajaran_id'] = null;
            if (!$request->is_bulanan) {
                $validated['bulan_pembayaran'] = null;
            }
        } elseif ($request->jenis_pembayaran === 'insidentil') {
            $validated['is_bulanan'] = false;
        }

        JenisTagihan::create($validated);

        // Generate tagihan santri untuk jenis tagihan baru
        $this->generateTagihanSantri();

        return redirect()->route('keuangan.jenis-tagihan.index')
            ->with('success', 'Data berhasil ditambahkan dan tagihan santri telah dibuat otomatis');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisTagihan $jenisTagihan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        $tahunAjarans = TahunAjaran::all();
        return view('keuangan.jenis_tagihan.edit', compact('jenisTagihan', 'tahunAjarans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_tagihan' => 'required|in:Rutin,Insidental',
            'is_bulanan' => 'required|in:0,1',
            'nominal' => 'required|numeric|min:0',
            'is_nominal_per_kelas' => 'required|in:0,1',
        ]);
        $jenisTagihan->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kategori_tagihan' => $request->kategori_tagihan,
            'is_bulanan' => $request->is_bulanan,
            'nominal' => $request->nominal,
            'is_nominal_per_kelas' => $request->is_nominal_per_kelas,
        ]);

        // Generate tagihan santri jika ada perubahan yang mempengaruhi
        $this->generateTagihanSantri();

        return redirect()->route('keuangan.jenis-tagihan.index')
            ->with('success', 'Jenis Tagihan berhasil diperbarui dan tagihan santri telah disinkronkan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisTagihan $jenisTagihan)
    {
        try {
            $jenisTagihan->delete();
            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('error', 'Data tidak dapat dihapus karena masih digunakan');
        }
    }

    /**
     * Display class-specific nominal settings for a jenis tagihan.
     */
    public function showKelas($id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        $kelas = \App\Models\Kelas::orderBy('tingkat')->orderBy('nama')->get();
        
        return view('keuangan.jenis_tagihan.kelas', [
            'jenisTagihan' => $jenisTagihan,
            'kelas' => $kelas
        ]);
    }

    /**
     * Update class-specific nominal settings for a jenis tagihan.
     */
    public function updateKelas(Request $request, $id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        $nominal = $request->input('nominal', []);

        // Delete existing class-specific nominal settings
        \App\Models\JenisTagihanKelas::where('jenis_tagihan_id', $id)->delete();

        // Insert new class-specific nominal settings
        foreach ($nominal as $kelasId => $value) {
            if (!empty($value) && $value != $jenisTagihan->nominal) {
                \App\Models\JenisTagihanKelas::create([
                    'jenis_tagihan_id' => $id,
                    'kelas_id' => $kelasId,
                    'nominal' => $value
                ]);
            }
        }

        return redirect()->route('keuangan.jenis-tagihan.index')
            ->with('success', 'Nominal per kelas berhasil diperbarui');
    }

    /**
     * Generate tagihan santri untuk semua jenis tagihan
     */
    private function generateTagihanSantri()
    {
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        if (!$activeTahunAjaran) {
            return;
        }

        // Ambil semua santri aktif
        $santris = Santri::where('status', 'aktif')->with('kelasRelasi')->get();
        
        // Ambil semua jenis tagihan
        $jenisTagihans = JenisTagihan::all();

        foreach ($santris as $santri) {
            // Ambil kelas santri
            $kelasNames = $santri->kelasRelasi->pluck('nama')->toArray();
            
            foreach ($jenisTagihans as $jenisTagihan) {
                // Tentukan nominal berdasarkan kelas jika ada
                $nominal = $jenisTagihan->nominal;
                
                if ($jenisTagihan->is_nominal_per_kelas && !empty($kelasNames)) {
                    foreach ($kelasNames as $kelasName) {
                        $kelas = \App\Models\Kelas::where('nama', $kelasName)->first();
                        if ($kelas) {
                            $jenisTagihanKelas = JenisTagihanKelas::where('jenis_tagihan_id', $jenisTagihan->id)
                                ->where('kelas_id', $kelas->id)
                                ->first();
                            
                            if ($jenisTagihanKelas) {
                                $nominal = $jenisTagihanKelas->nominal;
                                break;
                            }
                        }
                    }
                }

                if ($jenisTagihan->kategori_tagihan === 'Rutin' && $jenisTagihan->is_bulanan) {
                    // Generate tagihan rutin bulanan
                    $bulanList = $this->generateBulanList($activeTahunAjaran);
                    
                    foreach ($bulanList as $bulan) {
                        $exists = TagihanSantri::where('santri_id', $santri->id)
                            ->where('jenis_tagihan_id', $jenisTagihan->id)
                            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                            ->where('bulan', $bulan)
                            ->exists();

                        if (!$exists) {
                            TagihanSantri::create([
                                'santri_id' => $santri->id,
                                'jenis_tagihan_id' => $jenisTagihan->id,
                                'tahun_ajaran_id' => $activeTahunAjaran->id,
                                'bulan' => $bulan,
                                'nominal_tagihan' => $nominal,
                                'nominal_dibayar' => 0,
                                'status' => 'aktif'
                            ]);
                        }
                    }
                } else {
                    // Generate tagihan insidentil atau rutin tahunan
                    $exists = TagihanSantri::where('santri_id', $santri->id)
                        ->where('jenis_tagihan_id', $jenisTagihan->id)
                        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                        ->exists();

                    if (!$exists) {
                        TagihanSantri::create([
                            'santri_id' => $santri->id,
                            'jenis_tagihan_id' => $jenisTagihan->id,
                            'tahun_ajaran_id' => $activeTahunAjaran->id,
                            'bulan' => $activeTahunAjaran->tahun_mulai . '-07', // Format bulan yang konsisten (Juli tahun mulai)
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 0,
                            'status' => 'aktif'
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Generate daftar bulan untuk tagihan rutin
     */
    private function generateBulanList($tahunAjaran)
    {
        $bulanList = [];
        $tahunMulai = (int) $tahunAjaran->tahun_mulai;
        $tahunAkhir = (int) $tahunAjaran->tahun_selesai;
        
        // Juli - Desember (tahun mulai)
        for ($i = 7; $i <= 12; $i++) {
            $bulanList[] = $tahunMulai . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        // Januari - Juni (tahun akhir)
        for ($i = 1; $i <= 6; $i++) {
            $bulanList[] = $tahunAkhir . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        return $bulanList;
    }
}

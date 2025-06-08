<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\JenisTagihanKelas;
use App\Models\BukuKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JenisTagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisTagihans = JenisTagihan::with(['tahunAjaran', 'bukuKas'])->paginate(10);
        $activeTahunAjaran = TahunAjaran::getActive();
        $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        return view('keuangan.jenis_tagihan.index', compact('jenisTagihans', 'activeTahunAjaran', 'bukuKasList', 'tahunAjarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        $bukuKasList = BukuKas::orderBy('nama_kas')->get();
        return view('keuangan.jenis_tagihan.create', compact('tahunAjarans', 'bukuKasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Simple validation for basic form fields
        $rules = [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_tagihan' => 'required|in:Rutin,Insidental',
            'is_bulanan' => 'required|in:0,1',
            'nominal' => 'required|numeric|min:0',
            'is_nominal_per_kelas' => 'required|in:0,1',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'tanggal_jatuh_tempo' => 'required|integer|min:1|max:31',
            'bulan_jatuh_tempo' => 'required|integer|min:0|max:12',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $jenisTagihan = JenisTagihan::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori_tagihan' => $request->kategori_tagihan,
                'is_bulanan' => $request->is_bulanan,
                'nominal' => $request->nominal,
                'is_nominal_per_kelas' => $request->is_nominal_per_kelas,
                'buku_kas_id' => $request->buku_kas_id,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'bulan_jatuh_tempo' => $request->bulan_jatuh_tempo,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil ditambahkan',
                    'data' => $jenisTagihan
                ]);
            }

            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data')
                ->withInput();
        }
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
        try {
            $jenisTagihan = JenisTagihan::with('bukuKas')->findOrFail($id);
            
            if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get(['id', 'nama_kas', 'kode_kas', 'jenis_kas_id']);
                
                // Ensure all required fields are present with proper values
                $jenisTagihanData = [
                    'id' => $jenisTagihan->id,
                    'nama' => $jenisTagihan->nama,
                    'deskripsi' => $jenisTagihan->deskripsi ?? '',
                    'kategori_tagihan' => $jenisTagihan->kategori_tagihan,
                    'is_bulanan' => (int) $jenisTagihan->is_bulanan,
                    'nominal' => $jenisTagihan->nominal,
                    'is_nominal_per_kelas' => (int) $jenisTagihan->is_nominal_per_kelas,
                    'buku_kas_id' => $jenisTagihan->buku_kas_id ?? null,
                    'tahun_ajaran_id' => $jenisTagihan->tahun_ajaran_id,
                    'tanggal_jatuh_tempo' => $jenisTagihan->tanggal_jatuh_tempo,
                    'bulan_jatuh_tempo' => $jenisTagihan->bulan_jatuh_tempo,
                    'created_at' => $jenisTagihan->created_at,
                    'updated_at' => $jenisTagihan->updated_at,
                ];
                
                return response()->json([
                    'success' => true,
                    'jenisTagihan' => $jenisTagihanData,
                    'bukuKasList' => $bukuKasList
                ]);
            }
            
            $tahunAjarans = TahunAjaran::all();
            $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get();
            return view('keuangan.jenis_tagihan.edit', compact('jenisTagihan', 'tahunAjarans', 'bukuKasList'));
        } catch (\Exception $e) {
            \Log::error('Error in JenisTagihanController@edit: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage()
                ], 404);
            }
            
            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        
        $rules = [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_tagihan' => 'required|in:Rutin,Insidental',
            'is_bulanan' => 'required|in:0,1',
            'nominal' => 'required|numeric|min:0',
            'is_nominal_per_kelas' => 'required|in:0,1',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'tanggal_jatuh_tempo' => 'required|integer|min:1|max:31',
            'bulan_jatuh_tempo' => 'required|integer|min:0|max:12',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $jenisTagihan->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori_tagihan' => $request->kategori_tagihan,
                'is_bulanan' => $request->is_bulanan,
                'nominal' => $request->nominal,
                'is_nominal_per_kelas' => $request->is_nominal_per_kelas,
                'buku_kas_id' => $request->buku_kas_id,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'bulan_jatuh_tempo' => $request->bulan_jatuh_tempo,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis Tagihan berhasil diperbarui',
                    'data' => $jenisTagihan
                ]);
            }

            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Jenis Tagihan berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data')
                ->withInput();
        }
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
                                'tanggal_jatuh_tempo' => $this->calculateDueDateForBulanan($bulan),
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
                            'tanggal_jatuh_tempo' => $this->calculateDueDateForInsidentil($jenisTagihan, $activeTahunAjaran),
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

    /**
     * Calculate due date for tagihan bulanan
     */
    private function calculateDueDateForBulanan($bulan)
    {
        // Format bulan: YYYY-MM
        $parts = explode('-', $bulan);
        if (count($parts) === 2) {
            $tahun = (int) $parts[0];
            $bulanInt = (int) $parts[1];
            
            // Set jatuh tempo pada tanggal 15 setiap bulan
            return \Carbon\Carbon::createFromDate($tahun, $bulanInt, 15);
        }
        
        // Fallback: 30 hari dari sekarang
        return \Carbon\Carbon::now()->addDays(30);
    }

    /**
     * Calculate due date for tagihan insidentil/tahunan
     */
    private function calculateDueDateForInsidentil($jenisTagihan, $tahunAjaran)
    {
        // Untuk tagihan insidentil, set jatuh tempo 3 bulan dari tanggal pembuatan
        // Atau bisa disesuaikan berdasarkan kebijakan sekolah
        return \Carbon\Carbon::now()->addMonths(3);
    }

    /**
     * Generate tagihan santri for a specific jenis tagihan
     */
    public function generateTagihanSantriByJenisId($id)
    {
        try {
            $jenisTagihan = JenisTagihan::findOrFail($id);
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            
            if (!$activeTahunAjaran) {
                return redirect()->route('keuangan.jenis-tagihan.index')
                    ->with('error', 'Tidak ada tahun ajaran aktif untuk generate tagihan');
            }

            // Ambil semua santri aktif
            $santris = Santri::where('status', 'aktif')->with('kelasRelasi')->get();
            $tagihanCount = 0;
            
            foreach ($santris as $santri) {
                // Ambil kelas santri
                $kelasNames = $santri->kelasRelasi->pluck('nama')->toArray();
                
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
                                'tanggal_jatuh_tempo' => $this->calculateDueDateForBulanan($bulan),
                                'status' => 'aktif'
                            ]);
                            $tagihanCount++;
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
                            'tanggal_jatuh_tempo' => $this->calculateDueDateForInsidentil($jenisTagihan, $activeTahunAjaran),
                            'status' => 'aktif'
                        ]);
                        $tagihanCount++;
                    }
                }
            }

            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Berhasil generate ' . $tagihanCount . ' tagihan untuk jenis tagihan ' . $jenisTagihan->nama);
        } catch (\Exception $e) {
            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('error', 'Gagal generate tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel generated tagihan santri for a specific jenis tagihan
     */
    public function cancelTagihanSantriByJenisId($id)
    {
        try {
            $jenisTagihan = JenisTagihan::findOrFail($id);
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            
            if (!$activeTahunAjaran) {
                return redirect()->route('keuangan.jenis-tagihan.index')
                    ->with('error', 'Tidak ada tahun ajaran aktif untuk membatalkan tagihan');
            }

            // Hanya hapus tagihan yang belum dibayar atau dibayar sebagian
            $count = TagihanSantri::where('jenis_tagihan_id', $jenisTagihan->id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->where(function($query) {
                    $query->where('nominal_dibayar', 0)
                          ->orWhereColumn('nominal_dibayar', '<', 'nominal_tagihan');
                })
                ->delete();

            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Berhasil membatalkan ' . $count . ' tagihan untuk jenis tagihan ' . $jenisTagihan->nama);
        } catch (\Exception $e) {
            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('error', 'Gagal membatalkan tagihan: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\JenisTagihanKelas;
use App\Models\BukuKas;
use App\Models\Kelas;
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
        $jenisTagihans = JenisTagihan::with(['tahunAjaran', 'bukuKas'])->get();
        $activeTahunAjaran = TahunAjaran::getActive();
        $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        $academicYearMonths = $this->getAcademicYearMonths();
        
        return view('keuangan.jenis_tagihan.index', compact(
            'jenisTagihans', 
            'activeTahunAjaran', 
            'bukuKasList', 
            'tahunAjarans',
            'kelasList',
            'academicYearMonths'
        ));
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
        ];

        // For routine bills, we don't need date input as it's fixed to 10th
        if ($request->kategori_tagihan !== 'Rutin') {
            $rules['tanggal_jatuh_tempo'] = 'required|integer|min:1|max:31';
            $rules['bulan_jatuh_tempo'] = 'required|integer|min:0|max:12';
        }

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
            // For routine bills, always set due date to 10th
            $dataToCreate = [
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori_tagihan' => $request->kategori_tagihan,
                'is_bulanan' => $request->is_bulanan,
                'nominal' => $request->nominal,
                'is_nominal_per_kelas' => $request->is_nominal_per_kelas,
                'buku_kas_id' => $request->buku_kas_id,
                'tanggal_jatuh_tempo' => $request->kategori_tagihan === 'Rutin' ? 10 : $request->tanggal_jatuh_tempo,
                'bulan_jatuh_tempo' => $request->kategori_tagihan === 'Rutin' ? 0 : $request->bulan_jatuh_tempo,
            ];

            $jenisTagihan = JenisTagihan::create($dataToCreate);

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
     * Store a newly created insidental jenis tagihan with target options
     */
    public function storeInsidental(Request $request)
    {
        // Validation for insidental tagihan
        $rules = [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'nominal' => 'required|numeric|min:0',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'tanggal_jatuh_tempo' => 'required|integer|min:1|max:31',
            'bulan_jatuh_tempo' => 'required|integer|min:0|max:12',
            'bulan_pembayaran' => 'required|array|min:1',
            'bulan_pembayaran.*' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12',
            'target_type' => 'required|in:all,kelas,santri',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'santri_ids' => 'nullable|array',
            'santri_ids.*' => 'exists:santris,id',
        ];

        // Conditional validation based on target type
        if ($request->target_type === 'kelas') {
            $rules['kelas_ids'] = 'required|array|min:1';
        } elseif ($request->target_type === 'santri') {
            $rules['santri_ids'] = 'required|array|min:1';
        }

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
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            if (!$activeTahunAjaran) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.'
                    ], 422);
                }
                return redirect()->back()->withErrors(['tahun_ajaran_id' => 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.'])->withInput();
            }

            // Create jenis tagihan insidental (without is_nominal_per_kelas)
            $jenisTagihan = JenisTagihan::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori_tagihan' => 'Insidental',
                'is_bulanan' => 0, // Insidental is typically one-time
                'nominal' => $request->nominal,
                'is_nominal_per_kelas' => 0, // Set to 0 for insidental
                'buku_kas_id' => $request->buku_kas_id,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'bulan_jatuh_tempo' => $request->bulan_jatuh_tempo,
                'bulan_pembayaran' => $request->bulan_pembayaran,
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'target_type' => $request->target_type,
                'target_kelas' => $request->target_type === 'kelas' ? $request->kelas_ids : [],
                'target_santri' => $request->target_type === 'santri' ? $request->santri_ids : [],
                // Store data in both formats for compatibility
                'kelas_ids' => $request->target_type === 'kelas' ? json_encode($request->kelas_ids) : null,
                'santri_ids' => $request->target_type === 'santri' ? json_encode($request->santri_ids) : null,
                // Map to legacy fields for compatibility
                'tipe_pembayaran' => $request->target_type === 'kelas' ? 'kelas' : 'manual',
                'mode_santri' => $request->target_type === 'santri' ? 'individu' : ($request->target_type === 'all' ? 'semua' : null),
            ]);

            // Don't generate tagihan automatically, just save the jenis tagihan
            // Generate will be done manually using the generate button

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tagihan insidental berhasil dibuat. Gunakan tombol Generate untuk membuat tagihan.',
                    'data' => $jenisTagihan
                ]);
            }

            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Tagihan insidental berhasil dibuat. Gunakan tombol Generate untuk membuat tagihan.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Calculate due date for insidental tagihan based on specific month
     */
    private function calculateDueDateForInsidentalBulan($bulan, $jenisTagihan, $tahunAjaran)
    {
        $bulanInt = (int) $bulan;
        $tahun = (int) $tahunAjaran->tahun_mulai;
        
        // Adjust year if month is in the second half of academic year
        if ($bulanInt <= 6) {
            $tahun = (int) $tahunAjaran->tahun_selesai;
        }
        
        $tanggal = $jenisTagihan->tanggal_jatuh_tempo ?? 10;
        $tambahBulan = $jenisTagihan->bulan_jatuh_tempo ?? 0;
        
        try {
            $dueDate = \Carbon\Carbon::createFromDate($tahun, $bulanInt, $tanggal);
            if ($tambahBulan > 0) {
                $dueDate->addMonths($tambahBulan);
            }
            return $dueDate;
        } catch (\Exception $e) {
            // Fallback: 30 days from now
            return \Carbon\Carbon::now()->addDays(30);
        }
    }

    /**
     * Get santri list by kelas for AJAX
     */
    public function getSantriByKelas(Request $request)
    {
        $kelasId = $request->kelas_id;
        
        try {
            $santris = Santri::where('status', 'Aktif')
                ->where('kelas_id', $kelasId)
                ->orderBy('nama_santri')
                ->get(['id', 'nama_santri', 'nis']);

            return response()->json([
                'success' => true,
                'santris' => $santris->map(function($santri) {
                    return [
                        'id' => $santri->id,
                        'nama' => $santri->nama_santri,
                        'nis' => $santri->nis
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading santri: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all kelas for dropdown
     */
    public function getAllKelas()
    {
        $kelas = \App\Models\Kelas::orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'nama', 'tingkat']);

        return response()->json([
            'success' => true,
            'kelas' => $kelas
        ]);
    }

    /**
     * Get all santri for AJAX calls
     */
    public function getAllSantri()
    {
        try {
            $santri = Santri::with('kelas')
                           ->where('status', 'Aktif')
                           ->select('id', 'nama_santri', 'kelas_id')
                           ->orderBy('nama_santri')
                           ->get();
            
            return response()->json([
                'success' => true,
                'santri' => $santri->map(function($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->nama_santri,
                        'kelas' => $s->kelas ? [
                            'id' => $s->kelas->id,
                            'nama' => $s->kelas->nama
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllSantri: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data santri'
            ], 500);
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
            $jenisTagihan = JenisTagihan::with(['bukuKas'])->findOrFail($id);
            
            if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                $bukuKasList = BukuKas::where('is_active', true)->orderBy('nama_kas')->get(['id', 'nama_kas', 'kode_kas', 'jenis_kas_id']);
                
                // Get kelas data if target_type is kelas or tipe_pembayaran is kelas
                $kelasData = [];
                $kelasIds = [];
                
                // Check multiple possible sources for kelas data
                if ($jenisTagihan->target_kelas) {
                    $kelasIds = is_array($jenisTagihan->target_kelas) ? $jenisTagihan->target_kelas : json_decode($jenisTagihan->target_kelas, true);
                } elseif ($jenisTagihan->kelas_ids) {
                    $kelasIds = is_array($jenisTagihan->kelas_ids) ? $jenisTagihan->kelas_ids : json_decode($jenisTagihan->kelas_ids, true);
                } elseif ($jenisTagihan->tipe_pembayaran === 'kelas') {
                    // Get from relationship
                    $kelasIds = $jenisTagihan->kelas()->pluck('kelas.id')->toArray();
                }
                
                if ($kelasIds && is_array($kelasIds) && count($kelasIds) > 0) {
                    // Ensure kelasIds are integers
                    $kelasIds = array_map('intval', $kelasIds);
                    $kelasData = \App\Models\Kelas::whereIn('id', $kelasIds)->get(['id', 'nama'])->toArray();
                }
                
                // Get santri data if target_type is santri or mode_santri is individu
                $santriData = [];
                $santriIds = [];
                
                // Check multiple possible sources for santri data
                if ($jenisTagihan->target_santri) {
                    $santriIds = is_array($jenisTagihan->target_santri) ? $jenisTagihan->target_santri : json_decode($jenisTagihan->target_santri, true);
                } elseif ($jenisTagihan->santri_ids) {
                    $santriIds = is_array($jenisTagihan->santri_ids) ? $jenisTagihan->santri_ids : json_decode($jenisTagihan->santri_ids, true);
                } elseif ($jenisTagihan->mode_santri === 'individu') {
                    // Get from relationship
                    $santriIds = $jenisTagihan->santris()->pluck('santris.id')->toArray();
                }
                
                if ($santriIds && is_array($santriIds) && count($santriIds) > 0) {
                    // Ensure santriIds are integers
                    $santriIds = array_map('intval', $santriIds);
                    $santriData = \App\Models\Santri::whereIn('id', $santriIds)
                        ->with('kelas')
                        ->get(['id', 'nama_santri', 'kelas_id'])
                        ->map(function($santri) {
                            return [
                                'id' => $santri->id,
                                'nama_lengkap' => $santri->nama_santri,
                                'kelas' => $santri->kelas ? $santri->kelas->nama : 'Tidak ada kelas'
                            ];
                        })->toArray();
                }
                
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
                    'bulan_pembayaran' => array_map('intval', is_array($jenisTagihan->bulan_pembayaran) ? $jenisTagihan->bulan_pembayaran : json_decode($jenisTagihan->bulan_pembayaran ?? '[]', true)),
                    
                    // Map database fields to target_type for frontend
                    'target_type' => $this->mapDatabaseToTargetType($jenisTagihan),
                    
                    // Provide kelas_ids and santri_ids for frontend compatibility
                    'kelas_ids' => $kelasIds,
                    'santri_ids' => $santriIds,
                    'kelas_data' => $kelasData,
                    'santri_data' => $santriData,
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
        
        // Debug: Log semua data yang diterima
        \Log::info('Update JenisTagihan Request Data:', [
            'id' => $id,
            'all_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'target_type' => $request->target_type,
            'kelas_ids' => $request->kelas_ids,
            'has_kelas_ids' => $request->has('kelas_ids'),
            'bulan_pembayaran' => $request->bulan_pembayaran,
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'buku_kas_id' => $request->buku_kas_id
        ]);
        
        $rules = [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_tagihan' => 'required|in:Rutin,Insidental',
            'is_bulanan' => 'nullable|in:0,1',
            'nominal' => 'required|numeric|min:0',
            'is_nominal_per_kelas' => 'nullable|in:0,1',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'tanggal_jatuh_tempo' => 'nullable|integer|min:1|max:31',
            'bulan_jatuh_tempo' => 'nullable|integer|min:0|max:12',
        ];

        // Add validation for insidental specific fields
        if ($request->kategori_tagihan === 'Insidental') {
            $rules['bulan_pembayaran'] = 'nullable|array';
            $rules['bulan_pembayaran.*'] = 'string';
            $rules['target_type'] = 'nullable|in:all,kelas,santri';
            
            if ($request->target_type === 'kelas' && $request->has('kelas_ids')) {
                $rules['kelas_ids'] = 'array|min:1';
                $rules['kelas_ids.*'] = 'exists:kelas,id';
            } elseif ($request->target_type === 'santri' && $request->has('santri_ids')) {
                $rules['santri_ids'] = 'array|min:1';
                $rules['santri_ids.*'] = 'exists:santris,id';
            }
        }

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
            // Get active tahun ajaran for insidental bills
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            if (!$activeTahunAjaran && $request->kategori_tagihan === 'Insidental') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.'
                    ], 422);
                }
                return redirect()->back()->withErrors(['tahun_ajaran_id' => 'Tidak ada tahun ajaran aktif.'])->withInput();
            }
            
            $updateData = [
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori_tagihan' => $request->kategori_tagihan,
                'is_bulanan' => $request->is_bulanan ?? 0,
                'nominal' => $request->nominal,
                'is_nominal_per_kelas' => $request->is_nominal_per_kelas ?? 0,
                'buku_kas_id' => $request->buku_kas_id,
            ];
            
            // Untuk tagihan rutin, selalu set tanggal jatuh tempo ke 10
            if ($request->kategori_tagihan === 'Rutin') {
                $updateData['tanggal_jatuh_tempo'] = 10;
                $updateData['bulan_jatuh_tempo'] = 0;
                $updateData['bulan_pembayaran'] = null;
                $updateData['target_type'] = null;
            } else {
                // Untuk tagihan insidental, gunakan input user dan set tahun_ajaran_id
                $updateData['tahun_ajaran_id'] = $activeTahunAjaran->id;
                $updateData['tanggal_jatuh_tempo'] = $request->tanggal_jatuh_tempo ?? 10;
                $updateData['bulan_jatuh_tempo'] = $request->bulan_jatuh_tempo ?? 0;
                $updateData['bulan_pembayaran'] = $request->bulan_pembayaran ? json_encode($request->bulan_pembayaran) : null;
                
                // Mapping target_type ke field database yang sesuai
                if ($request->target_type === 'kelas') {
                    $updateData['tipe_pembayaran'] = 'kelas';
                    $updateData['mode_santri'] = null;
                    
                    // Store kelas data in both old and new field formats for compatibility
                    $kelasIds = $request->has('kelas_ids') ? $request->kelas_ids : [];
                    $updateData['kelas_ids'] = json_encode($kelasIds);
                    $updateData['target_kelas'] = json_encode($kelasIds);
                    $updateData['santri_ids'] = null;
                    $updateData['target_santri'] = null;
                    
                } elseif ($request->target_type === 'santri') {
                    $updateData['tipe_pembayaran'] = 'manual';
                    $updateData['mode_santri'] = 'individu';
                    
                    // Store santri data in both old and new field formats for compatibility
                    $santriIds = $request->has('santri_ids') ? $request->santri_ids : [];
                    $updateData['santri_ids'] = json_encode($santriIds);
                    $updateData['target_santri'] = json_encode($santriIds);
                    $updateData['kelas_ids'] = null;
                    $updateData['target_kelas'] = null;
                    
                } else {
                    // target_type === 'all'
                    $updateData['tipe_pembayaran'] = 'manual';
                    $updateData['mode_santri'] = 'semua';
                    $updateData['kelas_ids'] = null;
                    $updateData['target_kelas'] = null;
                    $updateData['santri_ids'] = null;
                    $updateData['target_santri'] = null;
                }
                
                // Store target_type directly for future compatibility
                $updateData['target_type'] = $request->target_type;
            }
            
            $jenisTagihan->update($updateData);

            // Handle target relationships for insidental
            if ($request->kategori_tagihan === 'Insidental') {
                // For insidental bills, we don't use pivot tables (kelas/santris relationships)
                // Instead, we store the data in JSON fields and create tagihan_santris records 
                // only when using the "Generate" function, not during create/update of jenis_tagihan
                
                // Clear existing pivot relationships (if any exist from previous versions)
                $jenisTagihan->kelas()->detach();
                $jenisTagihan->santris()->detach();
                
                // The target data is already stored in the JSON fields above (kelas_ids, santri_ids)
                // No need to create TagihanSantri records here - they will be created via generateTagihanSantriByJenisId()
            } else {
                // For routine bills, use pivot tables for relationships
                // Clear existing relationships
                $jenisTagihan->kelas()->detach();
                $jenisTagihan->santris()->detach();
                
                // Set new relationships based on target type (only for routine bills)
                if ($request->target_type === 'kelas' && $request->has('kelas_ids')) {
                    // Prepare data for pivot table with nominal value
                    $kelasData = [];
                    foreach ($request->kelas_ids as $kelasId) {
                        $kelasData[$kelasId] = ['nominal' => $request->nominal];
                    }
                    $jenisTagihan->kelas()->attach($kelasData);
                }
                // Note: For routine bills, we typically don't target individual santris via pivot
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis Tagihan berhasil diperbarui',
                    'data' => $jenisTagihan,
                    'debug' => [
                        'target_type' => $request->target_type,
                        'kelas_ids' => $request->kelas_ids,
                        'bulan_pembayaran' => $request->bulan_pembayaran
                    ]
                ]);
            }

            return redirect()->route('keuangan.jenis-tagihan.index')
                ->with('success', 'Jenis Tagihan berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating jenis tagihan: ' . $e->getMessage(), [
                'id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
    public function destroy($id)
    {
        try {
            $jenisTagihan = JenisTagihan::findOrFail($id);
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
            
            // Set jatuh tempo pada tanggal 10 setiap bulan
            return \Carbon\Carbon::createFromDate($tahun, $bulanInt, 10);
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

            // Filter santri berdasarkan target untuk tagihan insidental
            $santris = $this->getSantrisByTarget($jenisTagihan);
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

    /**
     * Preview insidental tagihan before generation
     */
    public function previewInsidental($id)
    {
        try {
            $jenisTagihan = JenisTagihan::with(['bukuKas', 'tahunAjaran'])
                                      ->where('kategori_tagihan', 'Insidental')
                                      ->findOrFail($id);
            
            // Get target santri based on jenis tagihan settings
            $santriList = $this->getTargetSantri($jenisTagihan);
            
            // Calculate totals
            $totalSantri = count($santriList);
            $bulanCount = is_array($jenisTagihan->bulan_pembayaran) ? count($jenisTagihan->bulan_pembayaran) : 1;
            $totalNominal = $totalSantri * $bulanCount * $jenisTagihan->nominal;
            
            // Prepare month names
            $bulanNames = [];
            if (is_array($jenisTagihan->bulan_pembayaran)) {
                $months = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                
                foreach ($jenisTagihan->bulan_pembayaran as $bulan) {
                    $bulanNames[] = $months[$bulan] ?? $bulan;
                }
            }
            
            return response()->json([
                'success' => true,
                'jenis_tagihan' => $jenisTagihan,
                'santri_list' => $santriList,
                'total_santri' => $totalSantri,
                'total_nominal' => $totalNominal,
                'bulan_names' => $bulanNames
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate tagihan for insidental billing
     */
    public function generateInsidental($id)
    {
        try {
            $jenisTagihan = JenisTagihan::with(['bukuKas', 'tahunAjaran'])
                                      ->where('kategori_tagihan', 'Insidental')
                                      ->findOrFail($id);
            
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            
            if (!$activeTahunAjaran) {
                throw new \Exception('Tidak ada tahun ajaran aktif');
            }
            
            // Get target santri
            $santriList = $this->getTargetSantri($jenisTagihan);
            
            $createdCount = 0;
            $bulanList = is_array($jenisTagihan->bulan_pembayaran) ? $jenisTagihan->bulan_pembayaran : ['01'];
            
            foreach ($santriList as $santri) {
                foreach ($bulanList as $bulan) {
                    // Check if tagihan already exists
                    $existingTagihan = TagihanSantri::where('santri_id', $santri['id'])
                                                   ->where('jenis_tagihan_id', $jenisTagihan->id)
                                                   ->where('bulan', $bulan)
                                                   ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                                                   ->first();
                    
                    if (!$existingTagihan) {
                        // Calculate due date
                        $currentYear = now()->year;
                        $dueDate = \Carbon\Carbon::createFromDate($currentYear, intval($bulan), $jenisTagihan->tanggal_jatuh_tempo ?? 10);
                        
                        // Add month offset if specified
                        if ($jenisTagihan->bulan_jatuh_tempo > 0) {
                            $dueDate->addMonths($jenisTagihan->bulan_jatuh_tempo);
                        }
                        
                        TagihanSantri::create([
                            'santri_id' => $santri['id'],
                            'jenis_tagihan_id' => $jenisTagihan->id,
                            'bulan' => $bulan,
                            'tahun_ajaran_id' => $activeTahunAjaran->id,
                            'nominal_tagihan' => $jenisTagihan->nominal,
                            'nominal_dibayar' => 0,
                            'nominal_keringanan' => 0,
                            'tanggal_jatuh_tempo' => $dueDate,
                            'status' => 'aktif',
                        ]);
                        
                        $createdCount++;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil digenerate',
                'created_count' => $createdCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get target santri based on jenis tagihan settings
     */
    private function getTargetSantri($jenisTagihan)
    {
        $targetType = $this->mapDatabaseToTargetType($jenisTagihan);
        
        switch ($targetType) {
            case 'kelas':
                // Filter santri berdasarkan kelas yang dipilih
                $kelasIds = $jenisTagihan->kelas_ids ?? [];
                
                if (empty($kelasIds)) {
                    // Fallback ke target_kelas jika kelas_ids kosong
                    $kelasIds = $jenisTagihan->target_kelas ?? [];
                }
                
                if (empty($kelasIds)) {
                    return []; // Return empty array jika tidak ada kelas yang dipilih
                }
                
                $santriQuery = Santri::with(['kelasAktif.kelas'])
                    ->where('status', 'aktif')
                    ->whereHas('kelasAktif', function($query) use ($kelasIds) {
                        $query->whereIn('kelas_id', $kelasIds);
                    });
                break;
                    
            case 'santri':
                // Filter santri berdasarkan santri yang dipilih
                $santriIds = $jenisTagihan->santri_ids ?? [];
                
                if (empty($santriIds)) {
                    // Fallback ke target_santri jika santri_ids kosong
                    $santriIds = $jenisTagihan->target_santri ?? [];
                }
                
                if (empty($santriIds)) {
                    return []; // Return empty array jika tidak ada santri yang dipilih
                }
                
                $santriQuery = Santri::with(['kelasAktif.kelas'])
                    ->where('status', 'aktif')
                    ->whereIn('id', $santriIds);
                break;
                    
            case 'all':
            default:
                // Semua santri aktif
                $santriQuery = Santri::with(['kelasAktif.kelas'])
                    ->where('status', 'aktif');
                break;
        }
        
        $santriList = $santriQuery->get()->map(function($santri) {
            return [
                'id' => $santri->id,
                'nama_lengkap' => $santri->nama_santri, // Fix field name
                'nis' => $santri->nis,
                'kelas' => $santri->kelasAktif && $santri->kelasAktif->kelas ? $santri->kelasAktif->kelas->nama : null
            ];
        })->toArray();
        
        return $santriList;
    }

    /**
     * Get months in active academic year
     */
    private function getAcademicYearMonths()
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            // Fallback to all months if no active academic year
            return [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
        }

        $startDate = Carbon::parse($activeTahunAjaran->tanggal_mulai);
        $endDate = Carbon::parse($activeTahunAjaran->tanggal_selesai);
        
        $months = [];
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthKey = $current->format('m');
            $monthName = $monthNames[$current->month];
            $months[$monthKey] = $monthName;
            $current->addMonth();
        }

        return $months;
    }

    /**
     * Search santri for target selection
     */
    public function searchSantri(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query minimal 2 karakter'
            ]);
        }
        
        try {
            $santri = Santri::with(['kelasAktif'])
                ->where('nama_santri', 'LIKE', '%' . $query . '%')
                ->where('status', 'Aktif')
                ->orderBy('nama_santri')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'santri' => $santri->map(function($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->nama_santri,
                        'nis' => $s->nis,
                        'kelas' => $s->kelasAktif ? [
                            'id' => $s->kelasAktif->kelas_id,
                            'nama' => $s->kelasAktif->kelas->nama
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching santri: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Map database fields to target_type for frontend compatibility
     */
    private function mapDatabaseToTargetType($jenisTagihan)
    {
        // Check multiple possible ways to determine target type
        
        // First, check if we have direct target_type field
        if (!empty($jenisTagihan->target_type)) {
            return $jenisTagihan->target_type;
        }
        
        // Check based on tipe_pembayaran field
        if ($jenisTagihan->tipe_pembayaran === 'kelas') {
            return 'kelas';
        }
        
        // Check based on mode_santri field
        if ($jenisTagihan->mode_santri === 'individu') {
            return 'santri';
        }
        
        // Check if there are target_kelas or kelas_ids
        $hasKelas = !empty($jenisTagihan->target_kelas) || !empty($jenisTagihan->kelas_ids);
        if ($hasKelas) {
            return 'kelas';
        }
        
        // Check if there are target_santri or santri_ids
        $hasSantri = !empty($jenisTagihan->target_santri) || !empty($jenisTagihan->santri_ids);
        if ($hasSantri) {
            return 'santri';
        }
        
        // Check relationships
        if ($jenisTagihan->relationLoaded('kelas') && $jenisTagihan->kelas->count() > 0) {
            return 'kelas';
        }
        
        if ($jenisTagihan->relationLoaded('santris') && $jenisTagihan->santris->count() > 0) {
            return 'santri';
        }
        
        // Default to 'all' if no specific target is found
        return 'all'; 
    }

    /**
     * Get santri berdasarkan target tagihan (all, kelas, atau santri spesifik)
     */
    private function getSantrisByTarget($jenisTagihan)
    {
        $targetType = $this->mapDatabaseToTargetType($jenisTagihan);
        
        switch ($targetType) {
            case 'kelas':
                // Filter santri berdasarkan kelas yang dipilih
                $kelasIds = $jenisTagihan->kelas_ids ?? [];
                
                if (empty($kelasIds)) {
                    // Fallback ke target_kelas jika kelas_ids kosong
                    $kelasIds = $jenisTagihan->target_kelas ?? [];
                }
                
                if (empty($kelasIds)) {
                    return collect(); // Return empty collection jika tidak ada kelas yang dipilih
                }
                
                return Santri::where('status', 'aktif')
                    ->with('kelasRelasi')
                    ->whereHas('kelasAktif', function($query) use ($kelasIds) {
                        $query->whereIn('kelas_id', $kelasIds);
                    })->get();
                    
            case 'santri':
                // Filter santri berdasarkan santri yang dipilih
                $santriIds = $jenisTagihan->santri_ids ?? [];
                
                if (empty($santriIds)) {
                    // Fallback ke target_santri jika santri_ids kosong
                    $santriIds = $jenisTagihan->target_santri ?? [];
                }
                
                if (empty($santriIds)) {
                    return collect(); // Return empty collection jika tidak ada santri yang dipilih
                }
                
                return Santri::where('status', 'aktif')
                    ->with('kelasRelasi')
                    ->whereIn('id', $santriIds)
                    ->get();
                    
            case 'all':
            default:
                // Semua santri aktif
                return Santri::where('status', 'aktif')
                    ->with('kelasRelasi')
                    ->get();
        }
    }

    // ...existing code...
}

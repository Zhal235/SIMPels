<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\JenisBukuKas;
use App\Services\KeuanganService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BukuKasController extends BaseKeuanganController
{
    protected $keuanganService;

    public function __construct(KeuanganService $keuanganService)
    {
        $this->keuanganService = $keuanganService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BukuKas::with('jenisBukuKas');

        // Apply common filters
        $query = $this->applyCommonFilters($query, $request);

        // Filter berdasarkan jenis kas
        if ($request->filled('jenis_kas')) {
            $query->whereHas('jenisBukuKas', function($q) use ($request) {
                $q->where('id', $request->jenis_kas);
            });
        }

        // Apply search
        if ($request->filled('search')) {
            $query = $this->applySearchFilter($query, $request->search);
        }

        $bukuKas = $query->orderBy('nama_kas')->paginate(10);

        // Statistik total saldo per jenis kas menggunakan service
        $statistik = $this->keuanganService->getBukuKasStatistics();

        // Dapatkan semua jenis kas untuk dropdown filter dan form
        $jenisKasList = JenisBukuKas::active()->orderBy('nama')->get();

        return view('keuangan.buku-kas.index', compact('bukuKas', 'statistik', 'jenisKasList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('keuangan.buku-kas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateInput($request, $this->getValidationRules());

        // Validate business rules
        $businessErrors = $this->keuanganService->validateBukuKasRules($validated);
        if (!empty($businessErrors)) {
            return $this->errorResponse(implode(', ', $businessErrors), 422);
        }

        $validated['saldo_saat_ini'] = $validated['saldo_awal'];
        $validated['is_active'] = $request->has('is_active');

        BukuKas::create($validated);

        return $this->successResponse(
            $request, 
            'Buku kas berhasil ditambahkan!',
            'keuangan.buku-kas.index'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BukuKas $bukuKas)
    {
        $bukuKas->load(['jenisTagihan', 'transaksiKas' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return $this->jsonResponse($request, $bukuKas, 'keuangan.buku-kas.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BukuKas $bukuKas)
    {
        return view('keuangan.buku-kas.edit', compact('bukuKas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BukuKas $bukuKas)
    {
        $validated = $this->validateInput($request, $this->getValidationRules($bukuKas->id));

        // Validate business rules
        $businessErrors = $this->keuanganService->validateBukuKasRules($validated, $bukuKas);
        if (!empty($businessErrors)) {
            return $this->errorResponse(implode(', ', $businessErrors), 422);
        }

        $validated['is_active'] = $request->has('is_active');

        $bukuKas->update($validated);

        return $this->successResponse(
            $request, 
            'Buku kas berhasil diperbarui!',
            'keuangan.buku-kas.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BukuKas $bukuKas)
    {
        $deleteCheck = $this->keuanganService->canDeleteBukuKas($bukuKas);
        
        if (!$deleteCheck['can_delete']) {
            return $this->errorResponse(
                'Tidak dapat menghapus buku kas: ' . implode(', ', $deleteCheck['messages']),
                400
            );
        }

        $bukuKas->delete();

        return $this->successJsonResponse('Buku kas berhasil dihapus!');
    }

    /**
     * Get buku kas untuk dropdown/select
     */
    public function getBukuKasList()
    {
        $formattedBukuKas = $this->keuanganService->getBukuKasForDropdown();
        
        return $this->successJsonResponse('Data berhasil diambil', $formattedBukuKas);
    }

    /**
     * Apply search filter specific to BukuKas
     */
    protected function applySearchFilter($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_kas', 'like', "%{$search}%")
              ->orWhere('kode_kas', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%");
        });
    }

    /**
     * Get validation rules for BukuKas
     */
    protected function getValidationRules($id = null): array
    {
        $rules = [
            'nama_kas' => 'required|string|max:255',
            'kode_kas' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'jenis_kas_id' => 'required|exists:jenis_buku_kas,id',
            'saldo_awal' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ];

        if ($id) {
            $rules['nama_kas'] .= ',unique:buku_kas,nama_kas,' . $id;
            $rules['kode_kas'] .= ',unique:buku_kas,kode_kas,' . $id;
        } else {
            $rules['nama_kas'] .= '|unique:buku_kas,nama_kas';
            $rules['kode_kas'] .= '|unique:buku_kas,kode_kas';
        }

        return $rules;
    }
}

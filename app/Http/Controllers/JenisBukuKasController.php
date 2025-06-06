<?php

namespace App\Http\Controllers;

use App\Models\JenisBukuKas;
use App\Models\BukuKas;
use App\Services\KeuanganService;
use Illuminate\Http\Request;

class JenisBukuKasController extends BaseKeuanganController
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
        $query = JenisBukuKas::query();

        // Apply common filters
        $query = $this->applyCommonFilters($query, $request);

        // Apply search
        if ($request->filled('search')) {
            $query = $this->applySearchFilter($query, $request->search);
        }

        $jenisKas = $query->orderBy('nama')->get();

        // Hitung berapa buku kas yang menggunakan jenis kas ini menggunakan service
        $usageCount = $this->keuanganService->getJenisKasUsageCount();
        $jenisKas->each(function ($jenis) use ($usageCount) {
            $jenis->used_count = $usageCount[$jenis->id] ?? 0;
        });

        return view('keuangan.jenis-buku-kas.index', compact('jenisKas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateInput($request, $this->getValidationRules());

        // Validate business rules
        $businessErrors = $this->keuanganService->validateJenisKasRules($validated);
        if (!empty($businessErrors)) {
            return redirect()->back()
                ->withErrors($businessErrors)
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        JenisBukuKas::create($validated);

        return redirect()->route('jenis-buku-kas.index')
            ->with('success', 'Jenis kas berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jenisKas = JenisBukuKas::findOrFail($id);
        $bukuKas = BukuKas::where('jenis_kas_id', $id)->get();

        return view('keuangan.jenis-buku-kas.show', compact('jenisKas', 'bukuKas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jenisKas = JenisBukuKas::findOrFail($id);

        $validated = $this->validateInput($request, $this->getValidationRules($id));

        // Validate business rules
        $businessErrors = $this->keuanganService->validateJenisKasRules($validated, $jenisKas);
        if (!empty($businessErrors)) {
            return redirect()->back()
                ->withErrors($businessErrors)
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        $jenisKas->update($validated);

        return redirect()->route('jenis-buku-kas.index')
            ->with('success', 'Jenis kas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jenisKas = JenisBukuKas::findOrFail($id);
        
        $deleteCheck = $this->keuanganService->canDeleteJenisKas($jenisKas);
        
        if (!$deleteCheck['can_delete']) {
            return redirect()->route('jenis-buku-kas.index')
                ->with('error', implode(', ', $deleteCheck['messages']));
        }

        $jenisKas->delete();

        return redirect()->route('jenis-buku-kas.index')
            ->with('success', 'Jenis kas berhasil dihapus');
    }

    /**
     * Get jenis kas for dropdown
     */
    public function getForDropdown()
    {
        $jenisKas = $this->keuanganService->getJenisKasForDropdown();
        return $this->successJsonResponse('Data berhasil diambil', $jenisKas);
    }

    /**
     * Apply search filter specific to JenisBukuKas
     */
    protected function applySearchFilter($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('kode', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%");
        });
    }

    /**
     * Get validation rules for JenisBukuKas
     */
    protected function getValidationRules($id = null): array
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        if ($id) {
            $rules['nama'] .= ',unique:jenis_buku_kas,nama,' . $id;
            $rules['kode'] .= ',unique:jenis_buku_kas,kode,' . $id;
        } else {
            $rules['nama'] .= '|unique:jenis_buku_kas,nama';
            $rules['kode'] .= '|unique:jenis_buku_kas,kode';
        }

        return $rules;
    }
}

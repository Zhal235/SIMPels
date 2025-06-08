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
        try {
            $validated = $this->validateInput($request, $this->getValidationRules());

            // Validate business rules
            $businessErrors = $this->keuanganService->validateBukuKasRules($validated);
            if (!empty($businessErrors)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi bisnis gagal',
                        'errors' => ['business' => $businessErrors]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['business' => implode(', ', $businessErrors)])
                    ->withInput();
            }

            $validated['saldo_saat_ini'] = $validated['saldo_awal'];
            
            // Handle is_active field properly
            $validated['is_active'] = $request->input('is_active') == '1' || $request->input('is_active') === true;

            BukuKas::create($validated);

            return $this->successResponse(
                $request, 
                'Buku kas berhasil ditambahkan!',
                'keuangan.buku-kas.index'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->validator->errors()->toArray()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in BukuKasController@store: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem'
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['system' => 'Terjadi kesalahan sistem'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            // Cast to integer to avoid type issues
            $bukuKas = BukuKas::find((int) $id);
            
            if (!$bukuKas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data buku kas tidak ditemukan'
                ], 404);
            }
            
            // Simple response with guaranteed types
            $data = [
                'id' => $bukuKas->id,
                'nama_kas' => $bukuKas->nama_kas ?? '',
                'kode_kas' => $bukuKas->kode_kas ?? '',
                'jenis_kas_id' => $bukuKas->jenis_kas_id,
                'saldo_awal' => (float) $bukuKas->saldo_awal,
                'saldo_saat_ini' => (float) $bukuKas->saldo_saat_ini,
                'is_active' => (bool) $bukuKas->is_active,
                'deskripsi' => $bukuKas->deskripsi ?? ''
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in BukuKasController@show: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data buku kas: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            $validated = $this->validateInput($request, $this->getValidationRules($bukuKas->id));

            // Validate business rules
            $businessErrors = $this->keuanganService->validateBukuKasRules($validated, $bukuKas);
            if (!empty($businessErrors)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi bisnis gagal',
                        'errors' => ['business' => $businessErrors]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['business' => implode(', ', $businessErrors)])
                    ->withInput();
            }

            // Handle is_active field properly
            $validated['is_active'] = $request->input('is_active') == '1' || $request->input('is_active') === true;

            $bukuKas->update($validated);

            return $this->successResponse(
                $request, 
                'Buku kas berhasil diperbarui!',
                'keuangan.buku-kas.index'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->validator->errors()->toArray()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in BukuKasController@update: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem'
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['system' => 'Terjadi kesalahan sistem'])
                ->withInput();
        }
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
            'is_active' => 'nullable|in:0,1,true,false'
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

    /**
     * Get custom validation messages
     */
    protected function getValidationMessages(): array
    {
        return [
            'nama_kas.required' => 'Nama kas wajib diisi.',
            'nama_kas.string' => 'Nama kas harus berupa teks.',
            'nama_kas.max' => 'Nama kas maksimal 255 karakter.',
            'nama_kas.unique' => 'Nama kas sudah digunakan.',
            
            'kode_kas.required' => 'Kode kas wajib diisi.',
            'kode_kas.string' => 'Kode kas harus berupa teks.',
            'kode_kas.max' => 'Kode kas maksimal 50 karakter.',
            'kode_kas.unique' => 'Kode kas sudah digunakan.',
            
            'jenis_kas_id.required' => 'Jenis kas wajib dipilih.',
            'jenis_kas_id.exists' => 'Jenis kas yang dipilih tidak valid.',
            
            'saldo_awal.required' => 'Saldo awal wajib diisi.',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka.',
            'saldo_awal.min' => 'Saldo awal tidak boleh negatif.',
            
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'is_active.in' => 'Status aktif harus berupa nilai yang valid.'
        ];
    }

    /**
     * Validate input with custom messages
     */
    protected function validateInput(Request $request, array $rules): array
    {
        return $request->validate($rules, $this->getValidationMessages());
    }
}

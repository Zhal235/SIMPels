<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriKeuanganController extends Controller
{
    /**
     * Get all categories, optionally filtered by type
     */
    public function index(Request $request)
    {
        $query = KategoriKeuangan::query();
        
        if ($request->has('jenis') && in_array($request->jenis, ['pemasukan', 'pengeluaran', 'transfer'])) {
            $query->where('jenis_transaksi', $request->jenis);
        }
        
        $categories = $query->orderBy('nama_kategori')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    
    /**
     * Get category details
     */
    public function show($id)
    {
        $category = KategoriKeuangan::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }
    
    /**
     * Create new category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis_transaksi' => 'nullable|in:pemasukan,pengeluaran,transfer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        $category = KategoriKeuangan::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'jenis_transaksi' => $request->jenis_transaksi ?? 'pemasukan', // Default to pemasukan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data' => $category
        ], 201);
    }
    
    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        $category = KategoriKeuangan::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis_transaksi' => 'nullable|in:pemasukan,pengeluaran,transfer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'jenis_transaksi' => $request->jenis_transaksi ?? $category->jenis_transaksi
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category
        ]);
    }
    
    /**
     * Delete category
     */
    public function destroy($id)
    {
        $category = KategoriKeuangan::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
        
        // Check if the category is being used in transactions
        $inUse = TransaksiKas::where('kategori', $category->nama_kategori)->exists();
        
        if ($inUse) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena sedang digunakan dalam transaksi'
            ], 422);
        }
        
        $category->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}

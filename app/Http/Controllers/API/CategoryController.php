<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::withCount('products');

            // Filter berdasarkan status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Search berdasarkan nama
            if ($request->has('search') && $request->search != '') {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Check if pagination needed
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page', 15);
                $categories = $query->paginate($perPage);
            } else {
                $categories = $query->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $categoryData = $request->all();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('categories', $imageName, 'public');
                $categoryData['image'] = $imagePath;
            }

            $category = Category::create($categoryData);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error menambahkan kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category
     */
    public function show($id): JsonResponse
    {
        try {
            $category = Category::withCount('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $categoryData = $request->all();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('categories', $imageName, 'public');
                $categoryData['image'] = $imagePath;
            }

            $category->update($categoryData);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error memperbarui kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Check if category has products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih memiliki produk'
                ], 422);
            }

            // Delete image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories with their products
     */
    public function withProducts(): JsonResponse
    {
        try {
            $categories = Category::with(['products' => function($query) {
                $query->active()->orderBy('name');
            }])
            ->active()
            ->orderBy('name')
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kategori dengan produk berhasil diambil',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data kategori: ' . $e->getMessage()
            ], 500);
        }
    }
}

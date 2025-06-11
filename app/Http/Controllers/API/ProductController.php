<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category');

            // Filter berdasarkan kategori
            if ($request->has('category_id') && $request->category_id != '') {
                $query->where('category_id', $request->category_id);
            }

            // Filter berdasarkan status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Search berdasarkan nama atau SKU
            if ($request->has('search') && $request->search != '') {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
                });
            }

            // Filter produk dengan stok rendah
            if ($request->has('low_stock') && $request->low_stock == 'true') {
                $query->lowStock();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|integer|min:0',
                'sku' => 'nullable|string|unique:products,sku',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'minimum_stock' => 'nullable|integer|min:0',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $productData = $request->all();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $productData['image'] = $imagePath;
            }

            // Generate SKU jika tidak ada
            if (empty($productData['sku'])) {
                $productData['sku'] = 'PRD' . time();
            }

            $product = Product::create($productData);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product->load('category')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error menambahkan produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|integer|min:0',
                'sku' => 'nullable|string|unique:products,sku,' . $id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'minimum_stock' => 'nullable|integer|min:0',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $productData = $request->all();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $productData['image'] = $imagePath;
            }

            $product->update($productData);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $product->load('category')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error memperbarui produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            // Delete image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'stock' => 'required|integer|min:0',
                'type' => 'required|in:add,subtract,set'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $currentStock = $product->stock;
            $newStock = $request->stock;

            switch ($request->type) {
                case 'add':
                    $product->stock = $currentStock + $newStock;
                    break;
                case 'subtract':
                    $product->stock = max(0, $currentStock - $newStock);
                    break;
                case 'set':
                    $product->stock = $newStock;
                    break;
            }

            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Stok produk berhasil diperbarui',
                'data' => [
                    'product_id' => $product->id,
                    'previous_stock' => $currentStock,
                    'current_stock' => $product->stock
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error memperbarui stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products with low stock
     */
    public function lowStock(): JsonResponse
    {
        try {
            $products = Product::with('category')
                             ->lowStock()
                             ->active()
                             ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data produk dengan stok rendah berhasil diambil',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data produk: ' . $e->getMessage()
            ], 500);
        }
    }
}

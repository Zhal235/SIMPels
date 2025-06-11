<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['orderItems.product', 'servedByUser']);

            // Filter berdasarkan status
            if ($request->has('status') && $request->status != '') {
                $query->where('order_status', $request->status);
            }

            // Filter berdasarkan payment status
            if ($request->has('payment_status') && $request->payment_status != '') {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter berdasarkan tanggal
            if ($request->has('date') && $request->date != '') {
                $query->whereDate('order_date', $request->date);
            }

            // Filter berdasarkan range tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('order_date', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Search berdasarkan nomor order atau nama customer
            if ($request->has('search') && $request->search != '') {
                $query->where(function($q) use ($request) {
                    $q->where('order_number', 'like', '%' . $request->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $request->search . '%');
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'order_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_email' => 'nullable|email',
                'payment_method' => 'required|in:cash,card,qris,bank_transfer',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_percentage' => 'nullable|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk produk {$product->name}");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null
                ];
            }

            // Calculate tax and discount
            $discountAmount = $request->discount_amount ?? 0;
            $taxPercentage = $request->tax_percentage ?? 0;
            $taxAmount = ($subtotal - $discountAmount) * ($taxPercentage / 100);
            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'served_by' => auth()->id(),
                'notes' => $request->notes,
                'order_date' => now()
            ]);

            // Create order items and update stock
            foreach ($orderItems as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);

                // Update product stock
                $product = Product::find($itemData['product_id']);
                $product->decrement('stock', $itemData['quantity']);
            }

            DB::commit();

            // Load relationships
            $order->load(['orderItems.product', 'servedByUser']);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['orderItems.product', 'servedByUser'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'order_status' => 'required|in:pending,preparing,ready,completed,cancelled',
                'payment_status' => 'nullable|in:pending,paid,failed,refunded'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order->update([
                'order_status' => $request->order_status,
                'payment_status' => $request->payment_status ?? $order->payment_status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui',
                'data' => $order->load(['orderItems.product', 'servedByUser'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error memperbarui status pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel($id): JsonResponse
    {
        try {
            $order = Order::with('orderItems')->findOrFail($id);

            if ($order->order_status == 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan yang sudah selesai tidak dapat dibatalkan'
                ], 422);
            }

            DB::beginTransaction();

            // Restore product stock
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            // Update order status
            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => 'refunded'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan',
                'data' => $order->load(['orderItems.product', 'servedByUser'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's orders
     */
    public function todayOrders(): JsonResponse
    {
        try {
            $orders = Order::with(['orderItems.product', 'servedByUser'])
                          ->whereDate('order_date', today())
                          ->orderBy('order_date', 'desc')
                          ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan hari ini berhasil diambil',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', today()->format('Y-m-d'));
            $endDate = $request->get('end_date', today()->format('Y-m-d'));

            $stats = [
                'total_orders' => Order::whereBetween('order_date', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])->count(),
                
                'total_revenue' => Order::whereBetween('order_date', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])->where('payment_status', 'paid')->sum('total_amount'),
                
                'pending_orders' => Order::whereBetween('order_date', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])->where('order_status', 'pending')->count(),
                
                'completed_orders' => Order::whereBetween('order_date', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])->where('order_status', 'completed')->count(),
                
                'cancelled_orders' => Order::whereBetween('order_date', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])->where('order_status', 'cancelled')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistik pesanan berhasil diambil',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}

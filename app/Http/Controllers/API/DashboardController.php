<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'today'); // today, week, month, year
            
            // Set date range based on period
            switch ($period) {
                case 'today':
                    $startDate = today();
                    $endDate = today();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = today();
                    $endDate = today();
            }

            // Order statistics
            $orderStats = [
                'total_orders' => Order::whereBetween('order_date', [$startDate, $endDate])->count(),
                'completed_orders' => Order::whereBetween('order_date', [$startDate, $endDate])
                                          ->where('order_status', 'completed')->count(),
                'pending_orders' => Order::whereBetween('order_date', [$startDate, $endDate])
                                        ->where('order_status', 'pending')->count(),
                'cancelled_orders' => Order::whereBetween('order_date', [$startDate, $endDate])
                                           ->where('order_status', 'cancelled')->count(),
            ];

            // Revenue statistics
            $revenueStats = [
                'total_revenue' => Order::whereBetween('order_date', [$startDate, $endDate])
                                       ->where('payment_status', 'paid')
                                       ->sum('total_amount'),
                'pending_payments' => Order::whereBetween('order_date', [$startDate, $endDate])
                                           ->where('payment_status', 'pending')
                                           ->sum('total_amount'),
            ];

            // Product statistics
            $productStats = [
                'total_products' => Product::count(),
                'active_products' => Product::where('status', true)->count(),
                'low_stock_products' => Product::lowStock()->count(),
                'out_of_stock_products' => Product::where('stock', 0)->count(),
            ];

            // Category statistics
            $categoryStats = [
                'total_categories' => Category::count(),
                'active_categories' => Category::where('status', true)->count(),
            ];

            // Recent orders
            $recentOrders = Order::with(['orderItems.product', 'servedByUser'])
                                 ->orderBy('order_date', 'desc')
                                 ->limit(5)
                                 ->get();

            // Top selling products
            $topProducts = DB::table('order_items')
                            ->join('products', 'order_items.product_id', '=', 'products.id')
                            ->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->whereBetween('orders.order_date', [$startDate, $endDate])
                            ->where('orders.order_status', 'completed')
                            ->select(
                                'products.id',
                                'products.name',
                                'products.image',
                                DB::raw('SUM(order_items.quantity) as total_sold'),
                                DB::raw('SUM(order_items.subtotal) as total_revenue')
                            )
                            ->groupBy('products.id', 'products.name', 'products.image')
                            ->orderBy('total_sold', 'desc')
                            ->limit(5)
                            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Statistik dashboard berhasil diambil',
                'data' => [
                    'period' => $period,
                    'date_range' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d')
                    ],
                    'orders' => $orderStats,
                    'revenue' => $revenueStats,
                    'products' => $productStats,
                    'categories' => $categoryStats,
                    'recent_orders' => $recentOrders,
                    'top_products' => $topProducts
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil statistik dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales chart data
     */
    public function salesChart(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'week'); // week, month, year
            $chartData = [];

            switch ($period) {
                case 'week':
                    // Last 7 days
                    for ($i = 6; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $revenue = Order::whereDate('order_date', $date)
                                       ->where('payment_status', 'paid')
                                       ->sum('total_amount');
                        
                        $chartData[] = [
                            'date' => $date->format('Y-m-d'),
                            'label' => $date->format('D'),
                            'revenue' => $revenue,
                            'orders' => Order::whereDate('order_date', $date)->count()
                        ];
                    }
                    break;

                case 'month':
                    // Last 30 days
                    for ($i = 29; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $revenue = Order::whereDate('order_date', $date)
                                       ->where('payment_status', 'paid')
                                       ->sum('total_amount');
                        
                        $chartData[] = [
                            'date' => $date->format('Y-m-d'),
                            'label' => $date->format('M j'),
                            'revenue' => $revenue,
                            'orders' => Order::whereDate('order_date', $date)->count()
                        ];
                    }
                    break;

                case 'year':
                    // Last 12 months
                    for ($i = 11; $i >= 0; $i--) {
                        $date = now()->subMonths($i);
                        $startOfMonth = $date->copy()->startOfMonth();
                        $endOfMonth = $date->copy()->endOfMonth();
                        
                        $revenue = Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])
                                       ->where('payment_status', 'paid')
                                       ->sum('total_amount');
                        
                        $chartData[] = [
                            'date' => $date->format('Y-m'),
                            'label' => $date->format('M Y'),
                            'revenue' => $revenue,
                            'orders' => Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->count()
                        ];
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data chart penjualan berhasil diambil',
                'data' => [
                    'period' => $period,
                    'chart_data' => $chartData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data chart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment method statistics
     */
    public function paymentMethodStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'month');
            
            switch ($period) {
                case 'today':
                    $startDate = today();
                    $endDate = today();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }

            $paymentStats = Order::whereBetween('order_date', [$startDate, $endDate])
                                 ->where('payment_status', 'paid')
                                 ->select('payment_method', 
                                         DB::raw('COUNT(*) as count'),
                                         DB::raw('SUM(total_amount) as total'))
                                 ->groupBy('payment_method')
                                 ->get();

            return response()->json([
                'success' => true,
                'message' => 'Statistik metode pembayaran berhasil diambil',
                'data' => [
                    'period' => $period,
                    'payment_methods' => $paymentStats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil statistik metode pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock alert
     */
    public function lowStockAlert(): JsonResponse
    {
        try {
            $lowStockProducts = Product::with('category')
                                      ->lowStock()
                                      ->active()
                                      ->orderBy('stock')
                                      ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data produk stok rendah berhasil diambil',
                'data' => $lowStockProducts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data stok rendah: ' . $e->getMessage()
            ], 500);
        }
    }
}

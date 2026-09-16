<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

/**
 * Controller DashboardController (Admin)
 * Menyajikan analitik penjualan jersey, status stok gudang, dan pemantauan pesanan masuk.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Metrik Finansial & Pesanan
        $paidStatuses = [
            OrderStatus::PAID->value,
            OrderStatus::IN_PRODUCTION->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ];

        $monthlyRevenue = Order::whereIn('status', $paidStatuses)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('grand_total');

        $totalOrdersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $actionRequiredOrders = Order::whereIn('status', [OrderStatus::PAID->value, OrderStatus::IN_PRODUCTION->value])
            ->count();

        // 2. Metrik Inventori Gudang
        $totalStockWarehouse = ProductVariant::sum('stock');
        $totalProductsCount  = Product::count();
        $totalCategoriesCount = Category::count();

        $lowStockVariants = ProductVariant::where('stock', '<=', 3)
            ->with('product')
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        // 3. Pesanan Terbaru
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(7)
            ->get();

        // 4. Trend Penjualan 7 Hari Terakhir (Chart Data)
        $sevenDays = collect(range(6, 0))->map(function ($days) {
            return now()->subDays($days)->format('Y-m-d');
        });

        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->whereIn('status', $paidStatuses)
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartData   = [];

        foreach ($sevenDays as $day) {
            $chartLabels[] = date('d M', strtotime($day));
            $chartData[]   = (float) ($dailySales[$day] ?? 0);
        }

        return view('admin.dashboard', compact(
            'monthlyRevenue',
            'totalOrdersThisMonth',
            'actionRequiredOrders',
            'totalStockWarehouse',
            'totalProductsCount',
            'totalCategoriesCount',
            'lowStockVariants',
            'recentOrders',
            'chartLabels',
            'chartData'
        ));
    }
}

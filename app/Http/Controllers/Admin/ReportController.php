<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller ReportController (Admin)
 * Laporan Penjualan, Rekap Omset (Jersey, Sablon Nameset, Patch, Ongkir), dan Export CSV
 */
class ReportController extends Controller
{
    /**
     * Laporan Finansial & Penjualan
     */
    public function sales(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status    = $request->input('status');

        $query = Order::with(['items', 'payment'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        // Hitung Rincian Finansial
        $paidStatuses = [
            OrderStatus::PAID->value,
            OrderStatus::IN_PRODUCTION->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ];

        $paidOrders = $orders->whereIn('status', [
            OrderStatus::PAID,
            OrderStatus::IN_PRODUCTION,
            OrderStatus::SHIPPED,
            OrderStatus::COMPLETED,
        ]);

        $totalRevenue     = $paidOrders->sum('grand_total');
        $totalSubtotal    = $paidOrders->sum('subtotal_amount');
        $totalShipping    = $paidOrders->sum('shipping_cost');
        $totalOrdersCount = $paidOrders->count();

        // Hitung Total Item/Pcs Terjual
        $totalItemsSold = 0;
        foreach ($paidOrders as $ord) {
            $totalItemsSold += $ord->items->sum('quantity');
        }

        // Rata-rata Nilai Belanja per Order (AOV)
        $averageOrderValue = $totalOrdersCount > 0 ? ($totalRevenue / $totalOrdersCount) : 0;

        // Top 5 Jersey Terlaris
        $topJerseys = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('order', function ($q) use ($startDate, $endDate, $paidStatuses) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                  ->whereIn('status', $paidStatuses);
            })
            ->groupBy('product_name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        return view('admin.reports.sales', compact(
            'orders',
            'paidOrders',
            'totalRevenue',
            'totalSubtotal',
            'totalShipping',
            'totalOrdersCount',
            'totalItemsSold',
            'averageOrderValue',
            'topJerseys',
            'startDate',
            'endDate',
            'status'
        ));
    }

    /**
     * Laporan Audit Stok Gudang
     */
    public function stock(Request $request): View
    {
        $categoryId = $request->input('category_id');
        $filter = $request->input('filter');

        $query = ProductVariant::with(['product.category']);

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($filter === 'low') {
            $query->where('stock', '<=', 3);
        } elseif ($filter === 'out') {
            $query->where('stock', 0);
        }

        $variants = $query->orderBy('stock', 'asc')->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.reports.stock', compact('variants', 'categories'));
    }

    /**
     * Export Laporan Penjualan Format CSV
     */
    public function exportSalesCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $orders = Order::with(['items', 'payment'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        $filename = "Laporan_Penjualan_NgizanApparel_{$startDate}_{$endDate}.csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            
            // Header Kolom CSV
            fputcsv($handle, [
                'No. Order',
                'Tanggal',
                'Nama Pelanggan',
                'No. WhatsApp',
                'Kurir',
                'No. Resi',
                'Subtotal (Rp)',
                'Ongkos Kirim (Rp)',
                'Grand Total (Rp)',
                'Status Pesanan',
                'Metode Pembayaran',
                'Daftar Item (Nameset & Patch)',
            ]);

            foreach ($orders as $order) {
                $itemSpecs = [];
                foreach ($order->items as $it) {
                    $spec = "{$it->product_name} [Size {$it->size}] x{$it->quantity}";
                    if ($it->custom_name || $it->custom_number) {
                        $spec .= " (Sablon: {$it->custom_name} #{$it->custom_number})";
                    }
                    if ($it->selected_patch) {
                        $spec .= " (Patch: {$it->selected_patch})";
                    }
                    $itemSpecs[] = $spec;
                }

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->customer_name,
                    $order->customer_phone,
                    $order->courier_service_name ?: $order->courier_code,
                    $order->tracking_number ?: '-',
                    $order->subtotal_amount,
                    $order->shipping_cost,
                    $order->grand_total,
                    $order->status->label(),
                    $order->payment?->payment_type ?: 'Midtrans',
                    implode(' | ', $itemSpecs),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

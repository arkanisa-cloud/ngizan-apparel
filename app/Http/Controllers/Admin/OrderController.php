<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BiteshipService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller OrderController (Admin)
 * Mengelola antrian pesanan, status manifest produksi, booking kurir Biteship, dan cetak label pengiriman
 */
class OrderController extends Controller
{
    public function __construct(
        protected BiteshipService $biteship,
        protected WhatsAppService $whatsApp
    ) {}

    /**
     * Daftar Pesanan Masuk
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.product', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Detail Pesanan, Spesifikasi Sablon Nameset, & Pinpoint Alamat
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'payment']);

        $trackingInfo = null;
        if ($order->tracking_number && $order->courier_code) {
            $trackingInfo = $this->biteship->getTracking($order->tracking_number, $order->courier_code);
        }

        return view('admin.orders.show', compact('order', 'trackingInfo'));
    }

    /**
     * Perbarui Status Pesanan Secara Manual
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status'          => 'required|string|in:in_production,shipped,completed,cancelled',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $newStatus = OrderStatus::from($validated['status']);
        $updateData = ['status' => $newStatus];

        if (!empty($validated['tracking_number'])) {
            $updateData['tracking_number'] = trim($validated['tracking_number']);
            $updateData['courier_code'] = 'jnt';
            $updateData['courier_service_name'] = 'J&T Express (Gratis Ongkir)';
        }

        $order->update($updateData);

        if ($newStatus === OrderStatus::SHIPPED && $order->customer_phone) {
            try {
                $this->whatsApp->sendOrderShipped($order);
            } catch (Exception $e) {
                Log::warning('WhatsApp Send Order Shipped Failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Status pesanan #{$order->order_number} berhasil diubah menjadi {$newStatus->label()}.");
    }

    /**
     * Input Nomor Resi Pengiriman J&T Express & Otomatis Ubah Status ke Shipped
     * POST /admin/orders/{order}/tracking
     */
    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);

        $trackingNumber = trim($validated['tracking_number']);

        $order->update([
            'tracking_number'      => $trackingNumber,
            'courier_code'         => 'jnt',
            'courier_service_name' => 'J&T Express (Gratis Ongkir)',
            'status'               => OrderStatus::SHIPPED,
        ]);

        if ($order->customer_phone) {
            try {
                $this->whatsApp->sendOrderShipped($order);
            } catch (Exception $e) {
                Log::warning('WhatsApp Send Order Shipped Failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Nomor resi J&T {$trackingNumber} berhasil disimpan. Status pesanan otomatis diubah menjadi Telah Dikirim (Shipped) dan notifikasi WhatsApp terkirim.");
    }

    /**
     * Booking Kurir Penjemputan Otomatis via Biteship API
     * POST /admin/orders/{order}/biteship-booking
     */
    public function bookBiteshipCourier(Request $request, Order $order): RedirectResponse
    {
        if ($order->tracking_number) {
            return back()->with('error', "Pesanan ini sudah memiliki nomor resi Biteship: {$order->tracking_number}");
        }

        try {
            // Susun item payload untuk Biteship
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'name'        => $item->product_name . ' (' . $item->size . ')',
                    'description' => 'Jersey Football Kit' . ($item->custom_name ? ' [Custom #' . $item->custom_name . ']' : ''),
                    'value'       => (int) $item->subtotal,
                    'length'      => 15,
                    'width'       => 15,
                    'height'      => 5,
                    'weight'      => 250,
                    'quantity'    => (int) $item->quantity,
                ];
            }

            $payload = [
                'origin_area_id'      => config('services.biteship.origin_area_id', 'IDNP6IDJB164'),
                'destination_area_id' => $order->shipping_address_snapshot['biteship_area_id'] ?? null,
                'courier_company'     => $order->courier_code ?: 'sicepat',
                'courier_type'        => $order->courier_service_code ?: 'reg',
                'delivery_type'       => 'later',
                'order_note'          => $order->notes ?: 'Ngizan Apparel Jersey',
                'items'               => $items,
                'destination_contact_name'  => $order->customer_name,
                'destination_contact_phone' => $order->customer_phone,
                'destination_address'       => $order->shipping_address_snapshot['full_address'] ?? 'Alamat Pemesan',
            ];

            $booking = $this->biteship->createOrder($payload);

            $waybillId = $booking['waybill_id'] ?? $booking['courier_tracking_id'] ?? 'BTE-' . strtoupper(uniqid());
            $biteshipOrderId = $booking['id'] ?? null;

            $order->update([
                'biteship_order_id' => $biteshipOrderId,
                'tracking_number'   => $waybillId,
                'status'            => OrderStatus::SHIPPED,
            ]);

            // Kirim Notifikasi WhatsApp ke Customer dengan Nomor Resi
            try {
                $this->whatsApp->sendOrderShipped($order);
            } catch (Exception $e) {
                Log::warning('WhatsApp Send Order Shipped Failed: ' . $e->getMessage());
            }

            return back()->with('success', "Pickup kurir Biteship berhasil dibooking! Nomor Resi: {$waybillId}");

        } catch (Exception $e) {
            Log::error('Biteship Booking Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal booking kurir Biteship: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Label Pengiriman Thermal Printer (100x150mm)
     * GET /admin/orders/{order}/print-label
     */
    public function printShippingLabel(Order $order): View
    {
        $order->load(['items.product']);
        return view('admin.orders.print-label', compact('order'));
    }
}

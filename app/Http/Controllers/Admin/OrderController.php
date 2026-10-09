<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BinderbyteService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller OrderController (Admin)
 * Mengelola antrian pesanan, status manifest produksi, pelacakan J&T via Binderbyte, dan cetak label pengiriman
 */
class OrderController extends Controller
{
    public function __construct(
        protected BinderbyteService $binderbyte,
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
     * Detail Pesanan, Spesifikasi Sablon Nameset, & Pelacakan Ekspedisi J&T
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'payment']);

        $trackingInfo = null;
        if ($order->tracking_number) {
            $courier = $order->courier_code ?: 'jnt';
            $trackingInfo = $this->binderbyte->getTracking($order->tracking_number, $courier);

            if (!empty($trackingInfo['status']) && in_array(strtolower($trackingInfo['status']), ['delivered', 'selesai', 'sukses'])) {
                if ($order->status === OrderStatus::SHIPPED) {
                    $order->update([
                        'status'       => OrderStatus::COMPLETED,
                        'completed_at' => now(),
                    ]);
                    $order->refresh();
                }
            }
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
            'tracking_number' => 'required|string|min:6|max:50',
        ], [
            'tracking_number.required' => 'Nomor resi pengiriman wajib diisi.',
            'tracking_number.min'      => 'Nomor resi minimal terdiri dari 6 karakter.',
        ]);

        $trackingNumber = strtoupper(trim($validated['tracking_number']));

        // 1. Validasi karakter (hanya huruf, angka, dan strip)
        if (!preg_match('/^[A-Z0-9\-]+$/', $trackingNumber)) {
            return back()->withInput()->with('error', 'Nomor resi hanya boleh berisi huruf, angka, dan tanda hubung (-).');
        }

        $isSimulator = str_starts_with($trackingNumber, 'TEST-') || 
                        str_starts_with($trackingNumber, 'DEMO-') || 
                        str_starts_with($trackingNumber, 'MOCK-') ||
                        str_starts_with($trackingNumber, 'SIM-');

        if ($isSimulator) {
            // Validasi resi simulasi resmi
            $isValidSim = str_contains($trackingNumber, 'DELIVERED') || 
                          str_contains($trackingNumber, 'TRANSIT') || 
                          str_contains($trackingNumber, 'PICKUP') ||
                          str_contains($trackingNumber, 'SELESAI') ||
                          str_contains($trackingNumber, 'KIRIM');

            if (!$isValidSim) {
                return back()->withInput()->with('error', 'Nomor resi simulasi "' . $trackingNumber . '" tidak valid! Pilih salah satu tombol resmi: TEST-JNT-DELIVERED, TEST-JNT-TRANSIT, atau TEST-JNT-PICKUP.');
            }
        } else {
            // Validasi format nomor resi kurir J&T Express asli
            // Standar J&T Express: awalan JO, JX, JP, JS, JT, JD, JNT, TJNT, EZ atau deretan digit angka 8-20 karakter alfanumerik
            $isValidJntFormat = preg_match('/^(JO|JX|JP|JS|JT|JD|JNT|TJNT|EZ|[0-9]{8,20})[A-Z0-9]{4,18}$/i', $trackingNumber) ||
                                (strlen($trackingNumber) >= 8 && strlen($trackingNumber) <= 25 && ctype_alnum($trackingNumber));

            if (!$isValidJntFormat || strlen($trackingNumber) < 8) {
                return back()->withInput()->with('error', 'Format nomor resi "' . $trackingNumber . '" tidak valid! Resi J&T Express resmi umumnya berawalan JO, JX, JP, JT, JNT atau 8-20 karakter alfanumerik (Contoh: JO0325803121). Untuk uji coba, silakan gunakan tombol Quick Test Resi.');
            }
        }

        // Ambil info tracking awal via Binderbyte
        $trackingInfo = $this->binderbyte->getTracking($trackingNumber, 'jnt', true);

        $newStatus = OrderStatus::SHIPPED;
        $completedAt = null;

        if (!empty($trackingInfo['success']) && !empty($trackingInfo['status'])) {
            if (in_array(strtolower($trackingInfo['status']), ['delivered', 'selesai', 'sukses'])) {
                $newStatus = OrderStatus::COMPLETED;
                $completedAt = now();
            }
        }

        $order->update([
            'tracking_number'      => $trackingNumber,
            'courier_code'         => 'jnt',
            'courier_service_name' => 'J&T Express (Gratis Ongkir)',
            'status'               => $newStatus,
            'completed_at'         => $completedAt,
        ]);

        if ($order->customer_phone) {
            try {
                $this->whatsApp->sendOrderShipped($order);
            } catch (Exception $e) {
                Log::warning('WhatsApp Send Order Shipped Failed: ' . $e->getMessage());
            }
        }

        $statusMsg = $newStatus === OrderStatus::COMPLETED 
            ? "Nomor resi {$trackingNumber} berhasil disimpan. Paket berstatus TELAH TIBA (Completed) dan ulasan pelanggan telah aktif."
            : "Nomor resi {$trackingNumber} berhasil disimpan. Status pesanan diubah menjadi TELAH DIKIRIM (Shipped) dan terlacak otomatis via Binderbyte.";

        return back()->with('success', $statusMsg);
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

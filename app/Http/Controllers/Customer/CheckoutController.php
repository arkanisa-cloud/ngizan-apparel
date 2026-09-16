<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Services\BiteshipService;
use App\Services\InventoryService;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller CheckoutController
 * Mengelola alur checkout, pemilihan alamat presisi Biteship & Pinpoint Geocoding Leaflet.js,
 * pembuatan order dengan Anti-Overselling Guard, dan inisiasi pembayaran Midtrans Snap.
 */
class CheckoutController extends Controller
{
    public function __construct(
        protected BiteshipService $biteship,
        protected MidtransService $midtrans,
        protected WhatsAppService $whatsApp,
        protected InventoryService $inventory
    ) {}

    /**
     * 1. Halaman Form Checkout
     */
    public function index(): View|RedirectResponse
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')
                ->with('error', 'Tas belanja Anda kosong. Silakan pilih jersey terlebih dahulu.');
        }

        $addresses = ShippingAddress::where('user_id', Auth::id())
            ->orderBy('is_primary', 'desc')
            ->latest()
            ->get();

        $primaryAddress = $addresses->where('is_primary', true)->first() ?? $addresses->first();

        return view('customer.checkout', compact('cart', 'addresses', 'primaryAddress'));
    }

    /**
     * 2. API Endpoint: Autocomplete Pencarian Area Biteship Maps
     * GET /api/shipping/areas?query=...
     */
    public function searchAreas(Request $request): JsonResponse
    {
        $query = (string) $request->input('query', '');
        $areas = $this->biteship->searchAreas($query);

        return response()->json([
            'success' => true,
            'areas'   => $areas,
        ]);
    }

    /**
     * 3. API Endpoint: Kalkulasi Ongkos Kirim Multi-Kurir
     * POST /api/shipping/rates
     */
    public function getRates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'destination_area_id'   => 'nullable|string',
            'destination_latitude'  => 'nullable|numeric',
            'destination_longitude' => 'nullable|numeric',
        ]);

        $cart = Cart::where('user_id', Auth::id())->with(['items.product', 'items.variant'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart kosong.'], 400);
        }

        // Susun daftar item untuk kalkulasi berat
        $items = [];
        foreach ($cart->items as $item) {
            $items[] = [
                'name'        => $item->product->name . ' (' . ($item->variant->size ?? 'M') . ')',
                'description' => 'Jersey Football Kit',
                'value'       => (int) ($item->unit_price + $item->custom_fee),
                'length'      => 15,
                'width'       => 15,
                'height'      => 5,
                'weight'      => (int) ($item->product->weight_grams ?: 250),
                'quantity'    => (int) $item->quantity,
            ];
        }

        $rates = $this->biteship->getRates(
            destinationAreaId: $validated['destination_area_id'] ?? null,
            destLat: isset($validated['destination_latitude']) ? (float)$validated['destination_latitude'] : null,
            destLng: isset($validated['destination_longitude']) ? (float)$validated['destination_longitude'] : null,
            items: $items,
            couriers: 'jne,sicepat,jnt,anteraja,gojek,grab'
        );

        return response()->json([
            'success' => true,
            'pricing' => $rates,
        ]);
    }

    /**
     * 4. Proses Transaksi Checkout (DB Transaction + Anti-Overselling Guard + Midtrans Snap)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name'        => 'required|string|max:100',
            'phone_number'          => 'required|string|max:20',
            'full_address'          => 'required|string|max:500',
            'biteship_area_id'      => 'nullable|string|max:100',
            'province_name'         => 'nullable|string|max:100',
            'city_name'             => 'nullable|string|max:100',
            'district_name'         => 'nullable|string|max:100',
            'postal_code'           => 'nullable|string|max:10',
            'latitude'              => 'nullable|numeric',
            'longitude'             => 'nullable|numeric',
            'benchmark_notes'       => 'nullable|string|max:255',
            'courier_code'          => 'nullable|string|max:50',
            'courier_service_code'  => 'nullable|string|max:50',
            'courier_service_name'  => 'nullable|string|max:100',
            'shipping_cost'         => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string|max:500',
        ]);

        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tas belanja Anda kosong.'], 422);
            }
            return redirect()->route('shop.index')->with('error', 'Tas belanja kosong.');
        }

        try {
            $order = DB::transaction(function () use ($cart, $validated) {
                // 1. Simpan Alamat jika diminta
                if (!empty($validated['save_address'])) {
                    ShippingAddress::create([
                        'user_id'          => Auth::id(),
                        'label'            => 'Alamat Checkout',
                        'recipient_name'   => $validated['recipient_name'],
                        'phone_number'     => $validated['phone_number'],
                        'biteship_area_id' => $validated['biteship_area_id'] ?? null,
                        'province_name'    => $validated['province_name'] ?? 'Indonesia',
                        'city_name'        => $validated['city_name'] ?? '-',
                        'district_name'    => $validated['district_name'] ?? '-',
                        'postal_code'      => $validated['postal_code'] ?? '00000',
                        'latitude'         => $validated['latitude'] ?? null,
                        'longitude'        => $validated['longitude'] ?? null,
                        'full_address'     => $validated['full_address'],
                        'benchmark_notes'  => $validated['benchmark_notes'] ?? null,
                    ]);
                }

                // 2. Susun Snapshot Alamat Pengiriman
                $addressSnapshot = [
                    'recipient_name'   => $validated['recipient_name'],
                    'phone_number'     => $validated['phone_number'],
                    'biteship_area_id' => $validated['biteship_area_id'] ?? null,
                    'province_name'    => $validated['province_name'] ?? 'Indonesia',
                    'city_name'        => $validated['city_name'] ?? '-',
                    'district_name'    => $validated['district_name'] ?? '-',
                    'postal_code'      => $validated['postal_code'] ?? '00000',
                    'latitude'         => $validated['latitude'] ?? null,
                    'longitude'        => $validated['longitude'] ?? null,
                    'full_address'     => $validated['full_address'],
                    'benchmark_notes'  => $validated['benchmark_notes'] ?? null,
                ];

                $subtotal           = (float) $cart->total_price;
                $shippingCost       = (float) ($validated['shipping_cost'] ?? 0);
                $grandTotal         = $subtotal + $shippingCost;
                $courierCode        = $validated['courier_code'] ?? 'jnt';
                $courierServiceCode = $validated['courier_service_code'] ?? 'ez';
                $courierServiceName = $validated['courier_service_name'] ?? 'J&T Express (Gratis Ongkir)';

                // 3. Buat Record Order
                $order = Order::create([
                    'order_number'              => Order::generateOrderNumber(),
                    'user_id'                   => Auth::id(),
                    'status'                    => OrderStatus::PENDING_PAYMENT,
                    'subtotal_amount'           => $subtotal,
                    'shipping_cost'             => $shippingCost,
                    'grand_total'               => $grandTotal,
                    'courier_code'              => $courierCode,
                    'courier_service_code'      => $courierServiceCode,
                    'courier_service_name'      => $courierServiceName,
                    'customer_name'             => $validated['recipient_name'],
                    'customer_email'            => Auth::user()->email,
                    'customer_phone'            => $validated['phone_number'],
                    'shipping_address_snapshot' => $addressSnapshot,
                    'notes'                     => $validated['notes'] ?? null,
                    'expires_at'                => now()->addHours(2),
                ]);

                // 4. Salin Item Keranjang ke Order Items
                foreach ($cart->items as $cartItem) {
                    OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $cartItem->product_id,
                        'product_variant_id' => $cartItem->product_variant_id,
                        'product_name'       => $cartItem->product->name,
                        'size'               => $cartItem->variant->size ?? 'M',
                        'type'               => $cartItem->variant->type ?? 'Fans Issue',
                        'custom_name'        => $cartItem->custom_name,
                        'custom_number'      => $cartItem->custom_number,
                        'selected_patch'     => $cartItem->selected_patch,
                        'unit_price'         => $cartItem->unit_price,
                        'custom_fee'         => $cartItem->custom_fee,
                        'quantity'           => $cartItem->quantity,
                        'subtotal'           => $cartItem->total_price,
                    ]);
                }

                // 5. Anti-Overselling Guard (Kunci & Kurangi Stok Varian)
                $this->inventory->reserveStock($cart->items, $order);

                // 6. Kosongkan Keranjang
                $cart->items()->delete();

                return $order;
            });

            // 7. Buat Snap Token Midtrans
            $snapData = $this->midtrans->createSnapToken($order);

            // 8. Kirim Notifikasi WhatsApp
            try {
                $this->whatsApp->sendOrderCreated($order);
            } catch (Exception $e) {
                Log::warning('WhatsApp Notification Failed: ' . $e->getMessage());
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success'      => true,
                    'message'      => "Pesanan #{$order->order_number} berhasil dibuat!",
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'snap_token'   => $snapData['snap_token'],
                    'redirect_url' => $snapData['redirect_url'],
                ]);
            }

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', "Pesanan #{$order->order_number} berhasil dibuat! Silakan selesaikan pembayaran.");

        } catch (Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * 5. Daftar Riwayat Pesanan Pelanggan
     */
    public function indexOrders(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * 6. Detail Pesanan & Live Tracking
     */
    public function showOrder(Order $order): View
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['items.product', 'payment']);

        $trackingInfo = null;
        if ($order->tracking_number && $order->courier_code) {
            $trackingInfo = $this->biteship->getTracking($order->tracking_number, $order->courier_code);
        }

        return view('customer.orders.show', compact('order', 'trackingInfo'));
    }
}

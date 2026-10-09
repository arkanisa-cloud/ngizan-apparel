<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Services\BinderbyteService;
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
        protected BinderbyteService $binderbyte,
        protected MidtransService $midtrans,
        protected WhatsAppService $whatsApp,
        protected InventoryService $inventory
    ) {}

    /**
     * 1. Halaman Form Checkout
     */
    public function index(Request $request): View|RedirectResponse
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')
                ->with('error', 'Tas belanja Anda kosong. Silakan pilih jersey terlebih dahulu.');
        }

        $isPremium = Auth::user()?->isPremiumActive();
        foreach ($cart->items as $item) {
            if (!$item->variant) continue;
            $rawPrice = (float) $item->variant->final_price;
            $expectedUnitPrice = $isPremium ? (float) round($rawPrice * 0.95) : $rawPrice;

            if ((float) $item->unit_price !== (float) $expectedUnitPrice) {
                $item->unit_price = $expectedUnitPrice;
                $item->total_price = ($expectedUnitPrice + (float)$item->custom_fee) * $item->quantity;
                $item->save();
            }
        }

        // Support filtering only selected items from cart
        $selectedItemsParam = $request->input('selected_items', session('checkout_selected_items'));
        if ($selectedItemsParam) {
            if (is_string($selectedItemsParam)) {
                $selectedIds = explode(',', $selectedItemsParam);
            } else {
                $selectedIds = (array) $selectedItemsParam;
            }
            $selectedIds = array_filter(array_map('intval', $selectedIds));
            if (!empty($selectedIds)) {
                $filteredItems = $cart->items->whereIn('id', $selectedIds);
                if ($filteredItems->isNotEmpty()) {
                    $cart->setRelation('items', $filteredItems);
                    session(['checkout_selected_items' => $selectedIds]);
                }
            }
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
     * 2B. API Endpoint: Reverse Geocoding dari Titik Peta (OpenStreetMap Nominatim)
     * GET /api/shipping/reverse-geocode?latitude=...&longitude=...
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        if (!$lat || !$lng) {
            return response()->json(['success' => false, 'message' => 'Koordinat tidak valid.'], 400);
        }

        $geo = $this->biteship->reverseGeocode($lat, $lng);

        if (!$geo['success']) {
            return response()->json($geo, 500);
        }

        // Cari area Biteship yang cocok untuk mengunci biteship_area_id
        $matchedArea = null;
        $searchTerms = array_filter([$geo['postcode'], $geo['district'], $geo['village'], $geo['city']]);

        foreach ($searchTerms as $term) {
            if (!$term || strlen($term) < 3) continue;
            $areas = $this->biteship->searchAreas($term);
            if (!empty($areas)) {
                if (!empty($geo['postcode'])) {
                    foreach ($areas as $area) {
                        if (isset($area['postal_code']) && (string)$area['postal_code'] === (string)$geo['postcode']) {
                            $matchedArea = $area;
                            break 2;
                        }
                    }
                }
                $matchedArea = $areas[0];
                break;
            }
        }

        return response()->json([
            'success'      => true,
            'geo'          => $geo,
            'matched_area' => $matchedArea,
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
            'selected_items'        => 'nullable',
        ]);

        $cart = Cart::where('user_id', Auth::id())->with(['items.product', 'items.variant'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart kosong.'], 400);
        }

        $selectedItemsParam = $validated['selected_items'] ?? session('checkout_selected_items');
        $itemsToCalculate = $cart->items;
        if ($selectedItemsParam) {
            $selectedIds = is_string($selectedItemsParam) ? explode(',', $selectedItemsParam) : (array) $selectedItemsParam;
            $selectedIds = array_filter(array_map('intval', $selectedIds));
            if (!empty($selectedIds)) {
                $filtered = $cart->items->whereIn('id', $selectedIds);
                if ($filtered->isNotEmpty()) {
                    $itemsToCalculate = $filtered;
                }
            }
        }

        // Susun daftar item untuk kalkulasi berat
        $items = [];
        foreach ($itemsToCalculate as $item) {
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
            couriers: 'jnt'
        );

        // Kemitraan Eksklusif J&T Express: Gratis Ongkir Flat Rp 0
        $jntRates = [];
        foreach ($rates as $r) {
            if (strtolower($r['courier_code'] ?? '') === 'jnt') {
                $r['price'] = 0;
                $r['badge'] = 'Kemitraan Resmi (Gratis Ongkir)';
                $jntRates[] = $r;
            }
        }

        if (empty($jntRates)) {
            $jntRates[] = [
                'courier_code' => 'jnt',
                'courier_name' => 'J&T Express',
                'service_code' => 'ez',
                'service_name' => 'EZ (Reguler Kilat)',
                'price'        => 0,
                'badge'        => 'Kemitraan Resmi (Gratis Ongkir)',
                'etd'          => '1-2 Hari',
                'description'  => 'Gratis Ongkir Kemitraan Resmi J&T Express x Ngizan Apparel'
            ];
        }

        return response()->json([
            'success' => true,
            'pricing' => $jntRates,
        ]);
    }

    /**
     * 4. Proses Transaksi Checkout (DB Transaction + Anti-Overselling Guard + Midtrans Snap)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name'        => 'required|string|max:100',
            'phone_number'          => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{7,11}$/'],
            'label'                 => 'nullable|string|max:100',
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
            'selected_items'        => 'nullable',
        ], [
            'recipient_name.required' => 'Nama lengkap penerima wajib diisi.',
            'phone_number.required'   => 'Nomor WhatsApp aktif wajib diisi.',
            'phone_number.regex'      => 'Nomor WhatsApp harus berupa nomor seluler Indonesia yang valid (contoh: 081234567890 atau 6281234567890).',
            'full_address.required'   => 'Alamat lengkap wajib diisi.',
        ]);

        // Normalisasi Nomor WhatsApp (08...)
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $validated['phone_number']);
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }
        $validated['phone_number'] = $cleanPhone;

        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tas belanja Anda kosong.'], 422);
            }
            return redirect()->route('shop.index')->with('error', 'Tas belanja kosong.');
        }

        $selectedItemsParam = $validated['selected_items'] ?? session('checkout_selected_items');
        $itemsToCheckout = $cart->items;
        if ($selectedItemsParam) {
            $selectedIds = is_string($selectedItemsParam) ? explode(',', $selectedItemsParam) : (array) $selectedItemsParam;
            $selectedIds = array_filter(array_map('intval', $selectedIds));
            if (!empty($selectedIds)) {
                $filtered = $cart->items->whereIn('id', $selectedIds);
                if ($filtered->isNotEmpty()) {
                    $itemsToCheckout = $filtered;
                }
            }
        }

        if ($itemsToCheckout->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada item yang dipilih untuk checkout.'], 422);
            }
            return redirect()->route('customer.cart.index')->with('error', 'Silakan pilih produk yang ingin di-checkout.');
        }

        try {
            $order = DB::transaction(function () use ($cart, $itemsToCheckout, $validated) {
                // 1. Otomatis Simpan / Perbarui Alamat ke Buku Alamat Pelanggan
                $existingAddress = ShippingAddress::where('user_id', Auth::id())
                    ->where(function ($q) use ($validated) {
                        $q->where('full_address', $validated['full_address'])
                          ->orWhere('phone_number', $validated['phone_number']);
                    })
                    ->first();

                if ($existingAddress) {
                    $existingAddress->update([
                        'label'            => !empty($validated['label']) ? $validated['label'] : $existingAddress->label,
                        'recipient_name'   => $validated['recipient_name'],
                        'phone_number'     => $validated['phone_number'],
                        'biteship_area_id' => $validated['biteship_area_id'] ?? $existingAddress->biteship_area_id,
                        'province_name'    => $validated['province_name'] ?? $existingAddress->province_name,
                        'city_name'        => $validated['city_name'] ?? $existingAddress->city_name,
                        'district_name'    => $validated['district_name'] ?? $existingAddress->district_name,
                        'postal_code'      => $validated['postal_code'] ?? $existingAddress->postal_code,
                        'latitude'         => $validated['latitude'] ?? $existingAddress->latitude,
                        'longitude'        => $validated['longitude'] ?? $existingAddress->longitude,
                        'full_address'     => $validated['full_address'],
                        'benchmark_notes'  => $validated['benchmark_notes'] ?? $existingAddress->benchmark_notes,
                    ]);
                } else {
                    $hasAddress = ShippingAddress::where('user_id', Auth::id())->exists();
                    ShippingAddress::create([
                        'user_id'          => Auth::id(),
                        'label'            => !empty($validated['label']) ? $validated['label'] : ($hasAddress ? 'Alamat Checkout' : 'Rumah'),
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
                        'is_primary'       => !$hasAddress,
                    ]);
                }

                // Update nomor telepon user jika belum tersimpan di akun
                if (!Auth::user()->phone && !empty($validated['phone_number'])) {
                    Auth::user()->update(['phone' => $validated['phone_number']]);
                }

                // 2. Susun Snapshot Alamat Pengiriman
                $addressSnapshot = [
                    'label'            => !empty($validated['label']) ? $validated['label'] : 'Rumah',
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

                $subtotal           = (float) $itemsToCheckout->sum('total_price');
                $shippingCost       = (float) ($validated['shipping_cost'] ?? 0);
                $grandTotal         = $subtotal + $shippingCost;
                $courierCode        = !empty($validated['courier_code']) ? $validated['courier_code'] : 'jnt';
                $courierServiceCode = !empty($validated['courier_service_code']) ? $validated['courier_service_code'] : 'ez';
                $courierServiceName = !empty($validated['courier_service_name']) ? $validated['courier_service_name'] : 'J&T Express · EZ (Reguler Kilat)';

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
                foreach ($itemsToCheckout as $cartItem) {
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
                $this->inventory->reserveStock($itemsToCheckout, $order);

                // 6. Hapus hanya item yang di-checkout dari Keranjang
                CartItem::whereIn('id', $itemsToCheckout->pluck('id'))->delete();
                session()->forget('checkout_selected_items');

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

        // Auto sync pesanan pending yang sedang aktif ditampilkan
        foreach ($orders as $order) {
            if ($order->status === OrderStatus::PENDING_PAYMENT) {
                try {
                    $this->midtrans->syncOrderStatus($order);
                } catch (\Throwable $e) {
                    Log::warning("Auto-sync Midtrans error for order {$order->order_number}: " . $e->getMessage());
                }
            }
        }

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

        // Auto sync real-time dari Midtrans jika status masih pending payment
        if ($order->status === OrderStatus::PENDING_PAYMENT) {
            try {
                $this->midtrans->syncOrderStatus($order);
                $order->refresh();

                // Jika snap token belum ada, buatkan otomatis
                if (!$order->payment?->snap_token && !$order->status->isPaid()) {
                    $this->midtrans->createSnapToken($order);
                    $order->refresh();
                }
            } catch (\Throwable $e) {
                Log::warning("Auto-sync Midtrans error on showOrder for {$order->order_number}: " . $e->getMessage());
            }
        }

        $order->load(['items.product', 'payment']);

        $trackingInfo = null;
        if ($order->tracking_number) {
            $courier = $order->courier_code ?: 'jnt';
            $trackingInfo = $this->binderbyte->getTracking($order->tracking_number, $courier);

            // Auto-complete seketika jika paket dinyatakan telah sampai (delivered) oleh kurir
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

        return view('customer.orders.show', compact('order', 'trackingInfo'));
    }

    /**
     * 7. Endpoint AJAX Sinkronisasi Pembayaran Midtrans Manual/Callback
     */
    public function syncPayment(Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $updated = $this->midtrans->syncOrderStatus($order);
            $order->refresh();

            return response()->json([
                'success'      => true,
                'status'       => $order->status->value,
                'status_label' => $order->status->label(),
                'is_paid'      => $order->status->isPaid(),
                'updated'      => $updated,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

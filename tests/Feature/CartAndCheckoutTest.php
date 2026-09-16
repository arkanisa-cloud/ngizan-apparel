<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\StockReferenceType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->customer = User::where('role', 'customer')->first();
        $this->product = Product::first();
        $this->variant = $this->product->variants->first();
    }

    /**
     * 1. Test Customer can add jersey with nameset & patch to cart
     */
    public function test_customer_can_add_jersey_with_custom_nameset_to_cart(): void
    {
        $response = $this->actingAs($this->customer)->post(route('customer.cart.store'), [
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 2,
            'custom_name'        => 'BELLINGHAM',
            'custom_number'      => '5',
            'selected_patch'     => 'UCL Starball',
        ]);

        $response->assertRedirect(route('customer.cart.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 2,
            'custom_name'        => 'BELLINGHAM',
            'custom_number'      => '5',
            'selected_patch'     => 'UCL Starball',
        ]);
    }

    /**
     * 2. Test Customer can update quantity and remove item
     */
    public function test_customer_can_update_and_delete_cart_item(): void
    {
        $cart = Cart::firstOrCreate(['user_id' => $this->customer->id]);
        $item = CartItem::create([
            'cart_id'            => $cart->id,
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
            'unit_price'         => $this->variant->final_price,
            'custom_fee'         => 0,
            'total_price'        => $this->variant->final_price,
        ]);

        // Update Qty
        $updateResp = $this->actingAs($this->customer)->put(route('customer.cart.update', $item->id), [
            'quantity' => 3,
        ]);
        $updateResp->assertSessionHas('info');
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);

        // Delete Item
        $delResp = $this->actingAs($this->customer)->delete(route('customer.cart.destroy', $item->id));
        $delResp->assertSessionHas('info');
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    /**
     * 3. Test Checkout process: Anti-Overselling Guard, Snap Token & Cart Clearing
     */
    public function test_checkout_creates_order_reserves_stock_and_clears_cart(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'dummy-snap-token-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-snap-token-12345'
            ], 201),
        ]);

        $initialStock = $this->variant->stock;

        // Add item to cart
        $cart = Cart::firstOrCreate(['user_id' => $this->customer->id]);
        $cartItem = CartItem::create([
            'cart_id'            => $cart->id,
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity'           => 2,
            'unit_price'         => $this->variant->final_price,
            'custom_fee'         => 50000,
            'total_price'        => ($this->variant->final_price + 50000) * 2,
        ]);

        $checkoutResp = $this->actingAs($this->customer)->postJson(route('customer.checkout.store'), [
            'recipient_name'       => 'Alvaro Testing',
            'phone_number'         => '081299998888',
            'full_address'         => 'Jl. Testing No. 10 Jakarta',
            'biteship_area_id'     => 'IDNP6IDNC148IDND840IDZ12730',
            'district_name'        => 'Tebet',
            'city_name'            => 'Jakarta Selatan',
            'province_name'        => 'DKI Jakarta',
            'postal_code'          => '12810',
            'latitude'             => -6.2297,
            'longitude'            => 106.8555,
            'courier_code'         => 'sicepat',
            'courier_service_code' => 'reg',
            'courier_service_name' => 'SiCepat Regular',
            'shipping_cost'        => 18000,
        ]);

        $checkoutResp->assertStatus(200);
        $checkoutResp->assertJson(['success' => true]);

        // Verifikasi Order dibuat
        $this->assertDatabaseHas('orders', [
            'user_id'       => $this->customer->id,
            'customer_name' => 'Alvaro Testing',
            'status'        => OrderStatus::PENDING_PAYMENT->value,
            'shipping_cost' => 18000,
        ]);

        // Verifikasi Anti-Overselling Guard (Stok varian berkurang)
        $this->variant->refresh();
        $this->assertEquals($initialStock - 2, $this->variant->stock);

        // Verifikasi StockHistory pencatatan
        $this->assertDatabaseHas('stock_histories', [
            'product_variant_id' => $this->variant->id,
            'reference_type'     => StockReferenceType::ORDER_PLACED->value,
            'quantity_change'    => -2,
        ]);

        // Verifikasi Keranjang kosong
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /**
     * 4. Test Midtrans Webhook: Settlement updates order to PAID
     */
    public function test_midtrans_webhook_settlement_marks_order_as_paid(): void
    {
        $order = Order::create([
            'order_number'    => Order::generateOrderNumber(),
            'user_id'         => $this->customer->id,
            'status'          => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount' => 299000,
            'shipping_cost'   => 15000,
            'grand_total'     => 314000,
            'courier_code'    => 'jne',
            'customer_name'   => 'Customer Test',
            'customer_email'            => 'customer@ngizanapparel.com',
            'customer_phone'            => '08123456789',
            'shipping_address_snapshot' => ['recipient_name' => 'Customer Test', 'full_address' => 'Jakarta'],
        ]);

        $serverKey = config('services.midtrans.server_key');
        $orderId = $order->order_number;
        $statusCode = '200';
        $grossAmount = '314000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $response = $this->postJson(route('api.webhooks.midtrans'), [
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
            'payment_type'       => 'qris',
            'transaction_id'     => 'tx-midtrans-999',
        ]);

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals(OrderStatus::PAID, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertDatabaseHas('payments', [
            'order_id'           => $order->id,
            'transaction_status' => 'settlement',
        ]);
    }

    /**
     * 5. Test Midtrans Webhook: Expire cancels order and restores stock
     */
    public function test_midtrans_webhook_expire_restores_stock(): void
    {
        $initialStock = $this->variant->stock;

        $order = Order::create([
            'order_number'    => Order::generateOrderNumber(),
            'user_id'         => $this->customer->id,
            'status'          => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount' => 299000,
            'shipping_cost'   => 15000,
            'grand_total'     => 314000,
            'courier_code'    => 'jne',
            'customer_name'   => 'Customer Test',
            'customer_email'            => 'customer@ngizanapparel.com',
            'customer_phone'            => '08123456789',
            'shipping_address_snapshot' => ['recipient_name' => 'Customer Test', 'full_address' => 'Jakarta'],
        ]);

        $order->items()->create([
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'product_name'       => $this->product->name,
            'size'               => $this->variant->size,
            'type'               => $this->variant->type,
            'unit_price'         => 299000,
            'custom_fee'         => 0,
            'quantity'           => 3,
            'subtotal'           => 299000 * 3,
        ]);

        // Simulasikan stok terkurangi 3
        $this->variant->decrement('stock', 3);

        $serverKey = config('services.midtrans.server_key');
        $orderId = $order->order_number;
        $statusCode = '200';
        $grossAmount = '314000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $response = $this->postJson(route('api.webhooks.midtrans'), [
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'expire',
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(OrderStatus::EXPIRED, $order->status);

        // Verifikasi stok dipulihkan kembali
        $this->variant->refresh();
        $this->assertEquals($initialStock, $this->variant->stock);

        $this->assertDatabaseHas('stock_histories', [
            'product_variant_id' => $this->variant->id,
            'reference_type'     => StockReferenceType::RESTOCK_EXPIRED->value,
            'quantity_change'    => 3,
        ]);
    }

    /**
     * 6. Test Artisan command orders:cancel-expired cancels past orders and restores stock
     */
    public function test_artisan_cancel_expired_orders_restores_stock(): void
    {
        $initialStock = $this->variant->stock;

        $order = Order::create([
            'order_number'    => Order::generateOrderNumber(),
            'user_id'         => $this->customer->id,
            'status'          => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount' => 299000,
            'shipping_cost'   => 15000,
            'grand_total'     => 314000,
            'courier_code'    => 'jne',
            'customer_name'   => 'Customer Test',
            'customer_email'            => 'customer@ngizanapparel.com',
            'customer_phone'            => '08123456789',
            'shipping_address_snapshot' => ['recipient_name' => 'Customer Test', 'full_address' => 'Jakarta'],
            'expires_at'                => now()->subMinute(), // Expired
        ]);

        $order->items()->create([
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'product_name'       => $this->product->name,
            'size'               => $this->variant->size,
            'type'               => $this->variant->type,
            'unit_price'         => 299000,
            'custom_fee'         => 0,
            'quantity'           => 2,
            'subtotal'           => 598000,
        ]);

        $this->variant->decrement('stock', 2);

        Artisan::call('orders:cancel-expired');

        $order->refresh();
        $this->assertEquals(OrderStatus::EXPIRED, $order->status);

        $this->variant->refresh();
        $this->assertEquals($initialStock, $this->variant->stock);
    }
}

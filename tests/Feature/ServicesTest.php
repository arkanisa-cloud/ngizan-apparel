<?php

namespace Tests\Feature;

use App\Enums\JerseySize;
use App\Enums\JerseyType;
use App\Enums\OrderStatus;
use App\Enums\StockReferenceType;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\User;
use App\Services\BiteshipService;
use App\Services\ImageOptimizationService;
use App\Services\InventoryService;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Test ImageOptimizationService (Konversi WebP & Storage)
     */
    public function test_image_optimization_service_converts_and_stores(): void
    {
        Storage::fake('public');

        $service = new ImageOptimizationService();
        $file = UploadedFile::fake()->create('jersey-front.jpg', 100, 'image/jpeg');

        $savedPath = $service->convertToWebp($file, 'products');

        $this->assertNotNull($savedPath);
        $this->assertStringEndsWith('.webp', $savedPath);
        Storage::disk('public')->assertExists($savedPath);

        // Test delete
        $deleted = $service->deleteImage($savedPath);
        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($savedPath);
    }

    /**
     * 2. Test BiteshipService (Maps Autocomplete, Rates, & Tracking)
     */
    public function test_biteship_service_rates_and_search(): void
    {
        Http::fake([
            'api.biteship.com/v1/maps/areas*' => Http::response([
                'success' => true,
                'areas'   => [
                    [
                        'id'          => 'IDNP6IDJB165',
                        'name'        => 'Kebayoran Baru, Jakarta Selatan',
                        'postal_code' => 12110,
                    ],
                ],
            ], 200),
            'api.biteship.com/v1/rates/couriers' => Http::response([
                'success' => true,
                'pricing' => [
                    [
                        'courier_name'         => 'SiCepat',
                        'courier_code'         => 'sicepat',
                        'courier_service_code' => 'reg',
                        'price'                => 15000,
                    ],
                ],
            ], 200),
        ]);

        $biteship = new BiteshipService();

        // Search Areas
        $areas = $biteship->searchAreas('Kebayoran');
        $this->assertCount(1, $areas);
        $this->assertEquals('IDNP6IDJB165', $areas[0]['id']);

        // Rates
        $rates = $biteship->getRates(
            originAreaId: 'IDNP6IDJB164',
            destinationAreaId: 'IDNP6IDJB165',
            items: [['name' => 'Jersey', 'value' => 250000, 'weight' => 250, 'quantity' => 1]]
        );
        $this->assertCount(1, $rates);
        $this->assertEquals('sicepat', $rates[0]['courier_code']);
        $this->assertEquals(15000, $rates[0]['price']);
    }

    /**
     * 3. Test MidtransService (Snap Token & Signature Verification)
     */
    public function test_midtrans_service_snap_token_and_signature(): void
    {
        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token'        => 'dummy-snap-token-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-snap-token-12345',
            ], 201),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'order_number'              => Order::generateOrderNumber(),
            'user_id'                   => $user->id,
            'status'                    => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount'           => 250000,
            'shipping_cost'             => 15000,
            'grand_total'               => 265000,
            'customer_name'             => 'Alvaro Test',
            'customer_email'            => 'alvaro@test.com',
            'customer_phone'            => '08123456789',
            'shipping_address_snapshot' => ['city' => 'Jakarta'],
        ]);

        $midtrans = new MidtransService();
        $result = $midtrans->createSnapToken($order);

        $this->assertEquals('dummy-snap-token-12345', $result['snap_token']);
        $this->assertDatabaseHas('payments', [
            'order_id'   => $order->id,
            'snap_token' => 'dummy-snap-token-12345',
        ]);

        // Signature Verification Test
        $serverKey = config('services.midtrans.server_key');
        $validSignature = hash('sha512', $order->order_number . '200' . '265000' . $serverKey);

        $this->assertTrue($midtrans->verifySignature($order->order_number, '200', '265000', $validSignature));
        $this->assertFalse($midtrans->verifySignature($order->order_number, '200', '265000', 'invalid_signature'));
    }

    /**
     * 4. Test WhatsAppService (Fonnte Message Formatter & API)
     */
    public function test_whatsapp_service_sends_notifications(): void
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response([
                'status' => true,
                'detail' => 'Pesan terkirim',
            ], 200),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'order_number'              => Order::generateOrderNumber(),
            'user_id'                   => $user->id,
            'status'                    => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount'           => 250000,
            'shipping_cost'             => 15000,
            'grand_total'               => 265000,
            'customer_name'             => 'Alvaro Test',
            'customer_email'            => 'alvaro@test.com',
            'customer_phone'            => '08123456789',
            'shipping_address_snapshot' => ['city' => 'Bandung'],
        ]);

        $waService = new WhatsAppService();

        $res1 = $waService->sendOrderCreated($order);
        $this->assertTrue($res1['status']);

        $res2 = $waService->sendPaymentReceived($order);
        $this->assertTrue($res2['status']);

        $order->update(['tracking_number' => 'SICEPAT123456', 'courier_code' => 'sicepat']);
        $res3 = $waService->sendOrderShipped($order);
        $this->assertTrue($res3['status']);
    }

    /**
     * 5. Test InventoryService (Anti-Overselling Guard, Reserve, & Restore Stock)
     */
    public function test_inventory_service_reserve_and_restore_stock(): void
    {
        $category = Category::create(['name' => 'Eropa', 'slug' => 'eropa']);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Real Madrid Home',
            'slug'        => 'real-madrid-home',
            'base_price'  => 299000,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size'       => JerseySize::M->value,
            'type'       => JerseyType::PLAYER_ISSUE->value,
            'stock'      => 5,
            'sku'        => 'RMA-TEST-M',
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'order_number'              => Order::generateOrderNumber(),
            'user_id'                   => $user->id,
            'status'                    => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount'           => 299000,
            'shipping_cost'             => 0,
            'grand_total'               => 299000,
            'customer_name'             => 'Alvaro',
            'customer_email'            => 'alvaro@test.com',
            'customer_phone'            => '08123456',
            'shipping_address_snapshot' => [],
        ]);

        $orderItem = OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $product->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'size'               => $variant->size,
            'type'               => $variant->type,
            'unit_price'         => 299000,
            'quantity'           => 2,
            'subtotal'           => 598000,
        ]);

        $inventory = new InventoryService();

        // 1. Reserve Stock (5 - 2 = 3)
        $inventory->reserveStock([
            ['product_variant_id' => $variant->id, 'quantity' => 2],
        ], $order);

        $variant->refresh();
        $this->assertEquals(3, $variant->stock);
        $this->assertDatabaseHas('stock_histories', [
            'product_variant_id' => $variant->id,
            'reference_type'     => StockReferenceType::ORDER_PLACED->value,
            'quantity_change'    => -2,
            'stock_after'        => 3,
        ]);

        // 2. Test Anti-Overselling Guard (Mencoba pesan melebihi sisa stok 3)
        $this->expectException(Exception::class);
        $inventory->reserveStock([
            ['product_variant_id' => $variant->id, 'quantity' => 5], // Butuh 5, sisa hanya 3 -> Harus Throw Exception
        ], $order);
    }

    /**
     * 6. Test InventoryService Restore Stock
     */
    public function test_inventory_service_restore_stock(): void
    {
        $category = Category::create(['name' => 'Retro', 'slug' => 'retro']);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Man United 1999',
            'slug'        => 'mun-1999',
            'base_price'  => 349000,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size'       => JerseySize::L->value,
            'type'       => JerseyType::RETRO->value,
            'stock'      => 3,
            'sku'        => 'MUN-RESTORE-L',
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'order_number'              => Order::generateOrderNumber(),
            'user_id'                   => $user->id,
            'status'                    => OrderStatus::EXPIRED,
            'subtotal_amount'           => 349000,
            'shipping_cost'             => 0,
            'grand_total'               => 349000,
            'customer_name'             => 'Alvaro',
            'customer_email'            => 'alvaro@test.com',
            'customer_phone'            => '08123456',
            'shipping_address_snapshot' => [],
        ]);

        OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $product->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'size'               => $variant->size,
            'type'               => $variant->type,
            'unit_price'         => 349000,
            'quantity'           => 2,
            'subtotal'           => 698000,
        ]);

        $inventory = new InventoryService();
        $inventory->restoreStock($order, StockReferenceType::RESTOCK_EXPIRED);

        $variant->refresh();
        $this->assertEquals(5, $variant->stock); // 3 + 2 = 5
        $this->assertDatabaseHas('stock_histories', [
            'product_variant_id' => $variant->id,
            'reference_type'     => StockReferenceType::RESTOCK_EXPIRED->value,
            'quantity_change'    => 2,
            'stock_after'        => 5,
        ]);
    }

    /**
     * 7. Test Google OAuth 1-Click Login Controller
     */
    public function test_google_oauth_login_flow(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-unique-id-9988');
        $abstractUser->shouldReceive('getName')->andReturn('Alvaro Google User');
        $abstractUser->shouldReceive('getNickname')->andReturn('alvarogu');
        $abstractUser->shouldReceive('getEmail')->andReturn('alvaro.google@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email'     => 'alvaro.google@example.com',
            'google_id' => 'google-unique-id-9988',
            'role'      => 'customer',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\StockReferenceType;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBackofficeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->customer = User::where('role', 'customer')->first();
        $this->category = Category::first();
    }

    /**
     * 1. Test Admin Dashboard renders executive statistics
     */
    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard');
        $response->assertSee('Omset Bulan Ini');
        $response->assertSee('Stok Jersey Gudang');
    }

    /**
     * 2. Test Admin can create new Kit with Matrix Variants
     */
    public function test_admin_can_create_product_with_variant_matrix(): void
    {
        Storage::fake('public');

        $frontFile = UploadedFile::fake()->create('front.jpg', 100, 'image/jpeg');
        $backFile  = UploadedFile::fake()->create('back.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name'                 => 'Arsenal 2024/25 Away Kit',
            'category_id'          => $this->category->id,
            'base_price'           => 320000,
            'weight_grams'         => 250,
            'description'          => 'Official Away kit with African heritage design',
            'allow_custom_nameset' => 1,
            'custom_nameset_price' => 50000,
            'allow_patch'          => 1,
            'patch_price'          => 35000,
            'thumbnail_front'      => $frontFile,
            'thumbnail_back'       => $backFile,
            'variants'             => [
                ['size' => 'M', 'type' => 'Fans Issue', 'stock' => 15, 'price_adj' => 0],
                ['size' => 'L', 'type' => 'Player Issue', 'stock' => 8, 'price_adj' => 50000],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name'        => 'Arsenal 2024/25 Away Kit',
            'base_price'  => 320000,
        ]);

        $product = Product::where('name', 'Arsenal 2024/25 Away Kit')->first();
        $this->assertCount(2, $product->variants);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'size'       => 'L',
            'type'       => 'Player Issue',
            'stock'      => 8,
        ]);
    }

    /**
     * 3. Test Admin Stock In increases variant physical stock
     */
    public function test_admin_stock_in_increments_inventory(): void
    {
        $supplier = Supplier::create([
            'name'         => 'PT Konveksi Jersey Unggul',
            'contact_name' => 'Budi Supplier',
            'phone'        => '08123456789',
            'email'        => 'supplier@konveksi.com',
            'address'      => 'Bandung',
        ]);

        $variant = ProductVariant::first();
        $initialStock = $variant->stock;

        $response = $this->actingAs($this->admin)->post(route('admin.stock-ins.store'), [
            'supplier_id'        => $supplier->id,
            'product_variant_id' => $variant->id,
            'quantity'           => 20,
            'purchase_price'     => 120000,
            'invoice_number'     => 'INV-2026-001',
            'received_date'      => date('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.stock-ins.index'));
        $response->assertSessionHas('success');

        $variant->refresh();
        $this->assertEquals($initialStock + 20, $variant->stock);

        $this->assertDatabaseHas('stock_histories', [
            'product_variant_id' => $variant->id,
            'reference_type'     => StockReferenceType::MANUAL_IN->value,
            'quantity_change'    => 20,
        ]);
    }

    /**
     * 4. Test Admin Stock Out decrements variant stock
     */
    public function test_admin_stock_out_decrements_inventory(): void
    {
        $variant = ProductVariant::first();
        $initialStock = $variant->stock;

        $response = $this->actingAs($this->admin)->post(route('admin.stock-outs.store'), [
            'product_variant_id' => $variant->id,
            'quantity'           => 2,
            'reason'             => 'Damaged',
            'out_date'           => date('Y-m-d'),
            'notes'              => 'Cacat sablon',
        ]);

        $response->assertRedirect(route('admin.stock-outs.index'));
        $response->assertSessionHas('success');

        $variant->refresh();
        $this->assertEquals($initialStock - 2, $variant->stock);
    }

    /**
     * 5. Test Admin can book Biteship pickup and generates waybill resi
     */
    public function test_admin_can_book_biteship_courier(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/orders' => Http::response([
                'success' => true,
                'id' => 'bit_order_12345',
                'waybill_id' => 'TKP-00192837465',
                'status' => 'confirmed'
            ], 200),
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $order = Order::create([
            'order_number'    => Order::generateOrderNumber(),
            'user_id'         => $this->customer->id,
            'status'          => OrderStatus::PAID,
            'subtotal_amount' => 299000,
            'shipping_cost'   => 15000,
            'grand_total'     => 314000,
            'courier_code'    => 'sicepat',
            'courier_service_code' => 'reg',
            'customer_name'   => 'Customer Test',
            'customer_email'  => 'customer@ngizanapparel.com',
            'customer_phone'  => '08123456789',
            'shipping_address_snapshot' => [
                'recipient_name'   => 'Customer Test',
                'phone_number'     => '08123456789',
                'full_address'     => 'Jl. Testing',
                'biteship_area_id' => 'IDNP6IDNC148IDND840IDZ12730',
            ],
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.biteship.booking', $order->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(OrderStatus::SHIPPED, $order->status);
        $this->assertEquals('TKP-00192837465', $order->tracking_number);
    }

    /**
     * 6. Test Admin can export sales CSV
     */
    public function test_admin_can_export_sales_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export.sales'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }
}

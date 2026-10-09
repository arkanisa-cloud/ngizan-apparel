<?php

namespace Tests\Feature;

use App\Enums\JerseySize;
use App\Enums\JerseyType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\StockReferenceType;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Seeder dan Data Inisial
     */
    public function test_seeders_run_successfully(): void
    {
        $this->seed();

        // 1. Cek User Admin & Customer
        $this->assertDatabaseHas('users', [
            'email' => 'admin@ngizanapparel.com',
            'role'  => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'customer@ngizanapparel.com',
            'role'  => 'customer',
        ]);

        $admin = User::where('email', 'admin@ngizanapparel.com')->first();
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isCustomer());

        // 2. Cek Kategori
        $this->assertEquals(4, Category::count());
        $this->assertDatabaseHas('categories', ['slug' => 'klub-eropa']);
        $this->assertDatabaseHas('categories', ['slug' => 'tim-nasional']);
        $this->assertDatabaseHas('categories', ['slug' => 'retro-classics']);
        $this->assertDatabaseHas('categories', ['slug' => 'special-edition']);

        // 3. Cek Produk dan Varian
        $this->assertEquals(5, Product::count());
        $this->assertGreaterThan(20, ProductVariant::count());
        $this->assertGreaterThan(20, StockHistory::count());
    }

    /**
     * Test Relasi Product, Variant, dan StockHistory
     */
    public function test_product_variants_and_stock_relations(): void
    {
        $category = Category::create([
            'name' => 'Jersey Test',
            'slug' => 'jersey-test',
        ]);

        $product = Product::create([
            'category_id'          => $category->id,
            'name'                 => 'Jersey Uji Coba 2026',
            'slug'                 => 'jersey-uji-coba-2026',
            'description'          => 'Deskripsi uji coba',
            'base_price'           => 250000,
            'allow_custom_nameset' => true,
            'custom_nameset_price' => 50000,
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'size'             => JerseySize::L->value,
            'type'             => JerseyType::PLAYER_ISSUE->value,
            'stock'            => 10,
            'sku'              => 'TEST-2026-PI-L',
            'price_adjustment' => 40000,
        ]);

        $this->assertEquals(290000, $variant->final_price);
        $this->assertEquals('Rp 290.000', $variant->formatted_final_price);
        $this->assertEquals(10, $product->total_stock);
        $this->assertEquals('Rp 250.000', $product->formatted_price);

        // Mutasi Stok
        $history = StockHistory::create([
            'product_id'         => $product->id,
            'product_variant_id' => $variant->id,
            'reference_type'     => StockReferenceType::MANUAL_IN,
            'reference_id'       => 'TEST-REF-01',
            'quantity_change'    => 10,
            'stock_before'       => 0,
            'stock_after'        => 10,
        ]);

        $this->assertEquals(StockReferenceType::MANUAL_IN, $history->reference_type);
        $this->assertEquals($product->id, $variant->product->id);
    }

    /**
     * Test Order, OrderItem, dan Payment Enums & Relasi
     */
    public function test_order_and_payment_flow(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'order_number'              => Order::generateOrderNumber(),
            'user_id'                   => $user->id,
            'status'                    => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount'           => 300000,
            'shipping_cost'             => 20000,
            'grand_total'               => 320000,
            'customer_name'             => $user->name,
            'customer_email'            => $user->email,
            'customer_phone'            => '08123456789',
            'shipping_address_snapshot' => ['city' => 'Bandung', 'address' => 'Jl. Merdeka No. 1'],
        ]);

        $this->assertTrue($order->isPendingPayment());
        $this->assertEquals(OrderStatus::PENDING_PAYMENT, $order->status);

        $payment = Payment::create([
            'order_id'           => $order->id,
            'gross_amount'       => 320000,
            'transaction_status' => PaymentStatus::PENDING->value,
        ]);

        $this->assertEquals($order->id, $payment->order->id);
        $this->assertTrue($payment->isPending());
    }
}

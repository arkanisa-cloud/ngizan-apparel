<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ThreeFeaturesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_product_rating_and_reviews_render_on_detail_page(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::COMPLETED,
        ]);

        Review::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Kualitas jersey luar biasa, sablon sangat rapi!',
        ]);

        $response = $this->get(route('shop.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('5.0');
        $response->assertSee('1 ulasan');
        $response->assertSee('Kualitas jersey luar biasa, sablon sangat rapi!');
        $response->assertSee('Verified Buyer');
    }

    public function test_premium_member_sees_discount_badge_and_price(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $premiumUser = User::factory()->create([
            'role' => 'customer',
            'is_premium' => true,
            'premium_until' => now()->addYear(),
        ]);

        $expectedDiscountedPrice = 'Rp ' . number_format($product->base_price * 0.95, 0, ',', '.');

        $response = $this->actingAs($premiumUser)->get(route('shop.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('Member 5% OFF');
        $response->assertSee($expectedDiscountedPrice);
    }

    public function test_admin_inputs_jnt_waybill_automatically_sets_status_to_shipped(): void
    {
        $mockWa = Mockery::mock(WhatsAppService::class);
        $mockWa->shouldReceive('sendOrderShipped')->once()->andReturn(['status' => 'success']);
        $this->app->instance(WhatsAppService::class, $mockWa);

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer', 'phone' => '081234567890']);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'customer_phone' => '081234567890',
            'status' => OrderStatus::IN_PRODUCTION,
            'courier_code' => 'jnt',
            'shipping_cost' => 0,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.tracking', $order->id), [
            'tracking_number' => 'JNT0019283746',
        ]);

        $response->assertRedirect();
        $order->refresh();

        $this->assertEquals(OrderStatus::SHIPPED, $order->status);
        $this->assertEquals('JNT0019283746', $order->tracking_number);
        $this->assertEquals('jnt', $order->courier_code);
    }

    public function test_customer_submits_review_from_completed_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $product = Product::first();
        $this->assertNotNull($product);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::COMPLETED,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'size' => 'L',
            'type' => 'fans',
            'unit_price' => $product->base_price,
            'quantity' => 1,
            'subtotal' => $product->base_price,
        ]);

        $response = $this->actingAs($customer)->post(route('customer.reviews.store'), [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Bahan adem dan sablon nameset sangat rapi.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
        ]);
    }
}

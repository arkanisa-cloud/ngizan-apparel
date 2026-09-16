<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Product $product;
    protected Order $completedOrder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->customer = User::factory()->create();
        $this->product = Product::first();

        $this->completedOrder = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status'  => OrderStatus::COMPLETED,
        ]);

        OrderItem::create([
            'order_id'           => $this->completedOrder->id,
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->product->variants->first()->id,
            'product_name'       => $this->product->name,
            'size'               => 'L',
            'type'               => 'Fans Issue',
            'unit_price'         => 285000,
            'quantity'           => 1,
            'subtotal'           => 285000,
        ]);
    }

    public function test_verified_buyer_can_submit_review(): void
    {
        $response = $this->actingAs($this->customer)->post(route('customer.reviews.store'), [
            'product_id' => $this->product->id,
            'order_id'   => $this->completedOrder->id,
            'rating'     => 5,
            'comment'    => 'Kualitas jersey luar biasa, detail sablon sangat rapi!',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id'    => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id'   => $this->completedOrder->id,
            'rating'     => 5,
            'comment'    => 'Kualitas jersey luar biasa, detail sablon sangat rapi!',
        ]);
    }

    public function test_non_buyer_cannot_submit_review(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->post(route('customer.reviews.store'), [
            'product_id' => $this->product->id,
            'order_id'   => $this->completedOrder->id,
            'rating'     => 5,
            'comment'    => 'Ulasan fiktif',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews', [
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_buyer_with_uncompleted_order_cannot_submit_review(): void
    {
        $pendingOrder = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status'  => OrderStatus::SHIPPED,
        ]);

        OrderItem::create([
            'order_id'           => $pendingOrder->id,
            'product_id'         => $this->product->id,
            'product_variant_id' => $this->product->variants->first()->id,
            'product_name'       => $this->product->name,
            'size'               => 'M',
            'type'               => 'Player Issue',
            'unit_price'         => 285000,
            'quantity'           => 1,
            'subtotal'           => 285000,
        ]);

        $response = $this->actingAs($this->customer)->post(route('customer.reviews.store'), [
            'product_id' => $this->product->id,
            'order_id'   => $pendingOrder->id,
            'rating'     => 4,
            'comment'    => 'Paket belum sampai tapi coba review',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews', [
            'order_id' => $pendingOrder->id,
        ]);
    }

    public function test_duplicate_review_for_same_order_is_prevented(): void
    {
        Review::create([
            'user_id'    => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id'   => $this->completedOrder->id,
            'rating'     => 5,
            'comment'    => 'Review pertama',
        ]);

        $response = $this->actingAs($this->customer)->post(route('customer.reviews.store'), [
            'product_id' => $this->product->id,
            'order_id'   => $this->completedOrder->id,
            'rating'     => 4,
            'comment'    => 'Review kedua duplikat',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, Review::where('user_id', $this->customer->id)->count());
    }
}

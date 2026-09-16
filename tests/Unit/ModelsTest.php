<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\PremiumSubscription;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_premium_active_helper(): void
    {
        $user = User::factory()->create([
            'is_premium' => false,
            'premium_until' => null,
        ]);
        $this->assertFalse($user->isPremiumActive());

        $user->update([
            'is_premium' => true,
            'premium_until' => now()->addDays(30),
        ]);
        $this->assertTrue($user->isPremiumActive());

        $user->update([
            'is_premium' => true,
            'premium_until' => now()->subDay(),
        ]);
        $this->assertFalse($user->isPremiumActive());
    }

    public function test_product_get_final_price_applies_five_percent_discount_for_premium_member(): void
    {
        $this->seed();

        $product = Product::first();
        $basePrice = (float) $product->base_price;

        // Guest / null user: base price
        $this->assertEquals($basePrice, $product->getFinalPrice(null));

        // Regular user: base price
        $regularUser = User::where('role', 'customer')->first();
        $regularUser->update(['is_premium' => false, 'premium_until' => null]);
        $this->assertEquals($basePrice, $product->getFinalPrice($regularUser));

        // Premium user: 5% discount
        $premiumUser = User::factory()->create([
            'is_premium' => true,
            'premium_until' => now()->addYear(),
        ]);
        $expectedDiscountedPrice = round($basePrice * 0.95);
        $this->assertEquals($expectedDiscountedPrice, $product->getFinalPrice($premiumUser));
    }

    public function test_product_average_rating_and_reviews_relation(): void
    {
        $this->seed();

        $product = Product::first();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user1->id, 'status' => 'completed']);

        $this->assertEquals(0, $product->reviews_count);
        $this->assertEquals(0.0, $product->average_rating);

        Review::create([
            'user_id' => $user1->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Jersey mantap!',
        ]);

        $order2 = Order::factory()->create(['user_id' => $user2->id, 'status' => 'completed']);
        Review::create([
            'user_id' => $user2->id,
            'product_id' => $product->id,
            'order_id' => $order2->id,
            'rating' => 4,
            'comment' => 'Bagus sekali.',
        ]);

        $product->refresh();
        $this->assertEquals(2, $product->reviews_count);
        $this->assertEquals(4.5, $product->average_rating);
    }
}

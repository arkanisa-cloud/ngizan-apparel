<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that reviews table exists and has proper columns.
     */
    public function test_reviews_table_exists_with_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('reviews'));
        $this->assertTrue(Schema::hasColumns('reviews', [
            'id', 'user_id', 'product_id', 'order_id', 'rating', 'comment', 'created_at', 'updated_at'
        ]));
    }

    /**
     * Test that premium_subscriptions table exists and has proper columns.
     */
    public function test_premium_subscriptions_table_exists_with_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('premium_subscriptions'));
        $this->assertTrue(Schema::hasColumns('premium_subscriptions', [
            'id', 'user_id', 'subscription_code', 'amount', 'duration_days',
            'payment_status', 'snap_token', 'snap_redirect_url', 'paid_at', 'expires_at', 'created_at', 'updated_at'
        ]));
    }

    /**
     * Test that users table has is_premium and premium_until columns.
     */
    public function test_users_table_has_premium_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'is_premium', 'premium_until'
        ]));
    }
}

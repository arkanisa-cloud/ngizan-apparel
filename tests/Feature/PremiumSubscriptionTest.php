<?php

namespace Tests\Feature;

use App\Models\PremiumSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PremiumSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create([
            'is_premium' => false,
            'premium_until' => null,
        ]);

        Config::set('services.midtrans.server_key', 'test-server-key');
    }

    public function test_customer_can_initiate_premium_subscription_checkout(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token'        => 'snap-token-premium-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-premium-123',
            ], 201),
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson(route('customer.premium.subscribe'));

        $response->assertStatus(200);
        $response->assertJson([
            'success'    => true,
            'snap_token' => 'snap-token-premium-123',
        ]);

        $this->assertDatabaseHas('premium_subscriptions', [
            'user_id'        => $this->customer->id,
            'amount'         => 100000.00,
            'payment_status' => 'pending',
            'snap_token'     => 'snap-token-premium-123',
        ]);
    }

    public function test_midtrans_webhook_settlement_activates_user_premium(): void
    {
        $subscription = PremiumSubscription::create([
            'user_id'           => $this->customer->id,
            'subscription_code' => 'PREM-20260915-9988',
            'amount'            => 100000.00,
            'duration_days'     => 365,
            'payment_status'    => 'pending',
            'snap_token'        => 'dummy-token',
        ]);

        $serverKey = config('services.midtrans.server_key');
        $statusCode = '200';
        $grossAmount = '100000.00';
        $orderId = $subscription->subscription_code;
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
            'payment_type'       => 'qris',
            'transaction_id'     => 'trx-midtrans-prem-001',
        ];

        $response = $this->postJson(route('api.webhooks.midtrans'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $subscription->refresh();
        $this->assertEquals('settlement', $subscription->payment_status);
        $this->assertNotNull($subscription->paid_at);
        $this->assertNotNull($subscription->expires_at);

        $this->customer->refresh();
        $this->assertTrue($this->customer->is_premium);
        $this->assertNotNull($this->customer->premium_until);
        $this->assertTrue($this->customer->isPremiumActive());
    }

    public function test_midtrans_webhook_expire_marks_subscription_as_expired(): void
    {
        $subscription = PremiumSubscription::create([
            'user_id'           => $this->customer->id,
            'subscription_code' => 'PREM-20260915-1122',
            'amount'            => 100000.00,
            'duration_days'     => 365,
            'payment_status'    => 'pending',
            'snap_token'        => 'dummy-token-expire',
        ]);

        $serverKey = config('services.midtrans.server_key');
        $statusCode = '202';
        $grossAmount = '100000.00';
        $orderId = $subscription->subscription_code;
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'expire',
            'payment_type'       => 'qris',
        ];

        $response = $this->postJson(route('api.webhooks.midtrans'), $payload);

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertEquals('expire', $subscription->payment_status);

        $this->customer->refresh();
        $this->assertFalse($this->customer->isPremiumActive());
    }
}

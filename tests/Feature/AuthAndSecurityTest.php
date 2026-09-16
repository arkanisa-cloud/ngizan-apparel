<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customerA;
    protected User $customerB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->customerA = User::where('role', 'customer')->first();
        $this->customerB = User::create([
            'name'              => 'Customer B',
            'email'             => 'customerB@example.com',
            'password'          => bcrypt('password'),
            'role'              => 'customer',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * 1. Test Customer registration creates user with customer role and shopping cart
     */
    public function test_customer_registration_creates_account_and_cart(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'New Football Fan',
            'email'                 => 'fan@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'fan@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
    }

    /**
     * 2. Test Google OAuth login callback creates and logs in customer
     */
    public function test_google_oauth_callback_creates_and_authenticates_customer(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google_uid_123456');
        $abstractUser->shouldReceive('getEmail')->andReturn('google_fan@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Google Fan');
        $abstractUser->shouldReceive('getNickname')->andReturn('googlefan');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'google_fan@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google_uid_123456', $user->google_id);
        $this->assertEquals('customer', $user->role);
        $this->assertDatabaseHas('carts', ['user_id' => $user->id]);
    }

    /**
     * 3. Test Customer cannot access Admin routes (403 Forbidden)
     */
    public function test_customer_cannot_access_admin_dashboard_or_products(): void
    {
        $response = $this->actingAs($this->customerA)->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->customerA)->get(route('admin.products.index'));
        $response->assertStatus(403);
    }

    /**
     * 4. Test Guest cannot access Customer Cart/Checkout (Redirect to Login)
     */
    public function test_guest_cannot_access_customer_cart_or_checkout(): void
    {
        $response = $this->get(route('customer.cart.index'));
        $response->assertRedirect('/login');

        $response = $this->get(route('customer.checkout.index'));
        $response->assertRedirect('/login');
    }

    /**
     * 5. Test IDOR Protection: Customer A cannot view Customer B's order
     */
    public function test_customer_cannot_view_another_customer_order_idor_prevention(): void
    {
        $orderB = Order::create([
            'order_number'    => Order::generateOrderNumber(),
            'user_id'         => $this->customerB->id,
            'status'          => OrderStatus::PAID,
            'subtotal_amount' => 300000,
            'shipping_cost'   => 15000,
            'grand_total'     => 315000,
            'customer_name'   => 'Customer B',
            'customer_email'  => 'customerB@example.com',
            'customer_phone'  => '08123456789',
            'shipping_address_snapshot' => [
                'recipient_name' => 'Customer B',
                'phone_number'   => '08123456789',
                'full_address'   => 'Jl. Testing No. 2',
            ],
        ]);

        // Customer A mencoba mengakses order Customer B
        $response = $this->actingAs($this->customerA)->get(route('customer.orders.show', $orderB->id));
        $response->assertStatus(403);

        // Customer B yang memiliki order berhasil mengakses
        $responseOwner = $this->actingAs($this->customerB)->get(route('customer.orders.show', $orderB->id));
        $responseOwner->assertStatus(200);
    }

    /**
     * 6. Test Midtrans Webhook rejects forged/tampered signature
     */
    public function test_midtrans_webhook_rejects_invalid_signature(): void
    {
        $order = Order::create([
            'order_number'    => Order::generateOrderNumber(),
            'user_id'         => $this->customerA->id,
            'status'          => OrderStatus::PENDING_PAYMENT,
            'subtotal_amount' => 300000,
            'shipping_cost'   => 15000,
            'grand_total'     => 315000,
            'customer_name'   => 'Customer A',
            'customer_email'  => 'customerA@example.com',
            'customer_phone'  => '08123456789',
            'shipping_address_snapshot' => [
                'recipient_name' => 'Customer A',
                'phone_number'   => '08123456789',
                'full_address'   => 'Jl. Testing No. 1',
            ],
        ]);

        // Kirim payload dengan signature palsu
        $response = $this->postJson(route('api.webhooks.midtrans'), [
            'order_id'           => $order->order_number,
            'status_code'        => '200',
            'gross_amount'       => '315000.00',
            'signature_key'      => 'forged_fake_signature_hash_12345',
            'transaction_status' => 'settlement',
            'payment_type'       => 'qris',
        ]);

        $response->assertStatus(403);
        $order->refresh();
        $this->assertEquals(OrderStatus::PENDING_PAYMENT, $order->status);
    }

    /**
     * 7. Test Admin is allowed to access Backoffice
     */
    public function test_admin_is_allowed_to_access_backoffice(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\BiteshipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JntTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_biteship_service_get_tracking_fetches_jnt_status(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/trackings/JX1234567890/couriers/jnt' => Http::response([
                'success' => true,
                'status'  => 'in_transit',
                'history' => [
                    ['note' => 'Paket sedang dalam perjalanan', 'updated_at' => '2026-09-15 10:00:00']
                ]
            ], 200),
        ]);

        $service = new BiteshipService();
        $tracking = $service->getTracking('JX1234567890', 'jnt');

        $this->assertTrue($tracking['success']);
        $this->assertEquals('in_transit', $tracking['status']);
        $this->assertCount(1, $tracking['history']);
    }

    public function test_sync_jnt_tracking_command_auto_completes_delivered_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'status'          => OrderStatus::SHIPPED,
            'courier_code'    => 'jnt',
            'tracking_number' => 'JX9988776655',
            'shipped_at'      => now()->subDays(2),
            'completed_at'    => null,
        ]);

        Http::fake([
            'https://api.biteship.com/v1/trackings/JX9988776655/couriers/jnt' => Http::response([
                'success' => true,
                'status'  => 'delivered',
                'history' => [
                    ['note' => 'Paket telah sampai di tujuan', 'updated_at' => '2026-09-15 12:00:00']
                ]
            ], 200),
        ]);

        $this->artisan('orders:sync-tracking')
            ->assertExitCode(0);

        $order->refresh();
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function test_sync_jnt_tracking_command_keeps_in_transit_order_as_shipped(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'status'          => OrderStatus::SHIPPED,
            'courier_code'    => 'jnt',
            'tracking_number' => 'JX1122334455',
            'shipped_at'      => now()->subHours(12),
            'completed_at'    => null,
        ]);

        Http::fake([
            'https://api.biteship.com/v1/trackings/JX1122334455/couriers/jnt' => Http::response([
                'success' => true,
                'status'  => 'in_transit',
                'history' => [
                    ['note' => 'Paket sedang disortir di Gateway', 'updated_at' => '2026-09-15 08:00:00']
                ]
            ], 200),
        ]);

        $this->artisan('orders:sync-tracking')
            ->assertExitCode(0);

        $order->refresh();
        $this->assertEquals(OrderStatus::SHIPPED, $order->status);
        $this->assertNull($order->completed_at);
    }
}

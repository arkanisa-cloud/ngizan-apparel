<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\BinderbyteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JntTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_binderbyte_service_get_tracking_fetches_jnt_status(): void
    {
        Http::fake([
            'https://api.binderbyte.com/v1/track*' => Http::response([
                'status'  => 200,
                'message' => 'Successfully tracked AWB',
                'data'    => [
                    'summary' => [
                        'courier' => 'J&T Express',
                        'service' => 'EZ',
                        'status'  => 'ON PROCESS',
                        'date'    => '2026-09-15 10:00:00',
                        'desc'    => 'Paket sedang dalam perjalanan',
                    ],
                    'detail' => [
                        'origin'      => 'JAKARTA',
                        'destination' => 'SURABAYA',
                    ],
                    'history' => [
                        [
                            'desc'     => 'Paket sedang dalam perjalanan menuju kota tujuan',
                            'location' => 'JAKARTA',
                            'date'     => '2026-09-15 10:00:00',
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new BinderbyteService();
        $tracking = $service->getTracking('JX1234567890', 'jnt', true);

        $this->assertTrue($tracking['success']);
        $this->assertEquals('on_delivery', $tracking['status']);
        $this->assertEquals('ON PROCESS', $tracking['status_raw']);
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
            'https://api.binderbyte.com/v1/track*' => Http::response([
                'status'  => 200,
                'message' => 'Successfully tracked AWB',
                'data'    => [
                    'summary' => [
                        'courier' => 'J&T Express',
                        'service' => 'EZ',
                        'status'  => 'DELIVERED',
                        'date'    => '2026-09-15 12:00:00',
                        'desc'    => 'Paket telah sampai di tujuan (Diterima oleh YBS)',
                    ],
                    'detail' => [
                        'origin'      => 'JAKARTA',
                        'destination' => 'SURABAYA',
                    ],
                    'history' => [
                        [
                            'desc'     => 'Paket telah diterima oleh YBS',
                            'location' => 'SURABAYA',
                            'date'     => '2026-09-15 12:00:00',
                        ]
                    ]
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
            'https://api.binderbyte.com/v1/track*' => Http::response([
                'status'  => 200,
                'message' => 'Successfully tracked AWB',
                'data'    => [
                    'summary' => [
                        'courier' => 'J&T Express',
                        'service' => 'EZ',
                        'status'  => 'ON PROCESS',
                        'date'    => '2026-09-15 08:00:00',
                        'desc'    => 'Paket sedang disortir di Gateway',
                    ],
                    'detail' => [
                        'origin'      => 'JAKARTA',
                        'destination' => 'SURABAYA',
                    ],
                    'history' => [
                        [
                            'desc'     => 'Paket sedang disortir di Gateway',
                            'location' => 'JAKARTA',
                            'date'     => '2026-09-15 08:00:00',
                        ]
                    ]
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

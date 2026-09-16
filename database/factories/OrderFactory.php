<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number'              => 'NGZ-' . date('Ymd') . '-' . strtoupper($this->faker->bothify('??##')),
            'user_id'                   => User::factory(),
            'status'                    => OrderStatus::COMPLETED,
            'subtotal_amount'           => 285000.00,
            'shipping_cost'             => 0.00,
            'grand_total'               => 285000.00,
            'courier_code'              => 'jnt',
            'courier_service_code'      => 'ez',
            'courier_service_name'      => 'J&T Express (Gratis Ongkir)',
            'customer_name'             => $this->faker->name(),
            'customer_email'            => $this->faker->safeEmail(),
            'customer_phone'            => '081234567890',
            'shipping_address_snapshot' => [
                'recipient_name' => $this->faker->name(),
                'phone_number'   => '081234567890',
                'full_address'   => $this->faker->address(),
                'city_name'      => 'Jakarta Selatan',
                'postal_code'    => '12110',
            ],
            'expires_at'                => now()->addHours(2),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'status' => 'pending',
            'subtotal' => $this->faker->numberBetween(10000, 100000),
            'shipping_cost' => $this->faker->numberBetween(0, 5000),
            'total' => function (array $attributes) {
                return $attributes['subtotal'] + $attributes['shipping_cost'];
            },
            'shipping_address' => $this->faker->address(),
            'tracking_number' => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
            'tracking_number' => 'TRK-' . $this->faker->numerify('##########'),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}

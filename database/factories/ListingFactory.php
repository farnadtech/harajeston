<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['auction', 'direct_sale', 'hybrid']);
        $basePrice = fake()->randomFloat(2, 10000, 1000000);
        
        return [
            'seller_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'type' => $type,
            'category' => fake()->word(),
            
            // Auction fields
            'base_price' => $type !== 'direct_sale' ? $basePrice : null,
            'required_deposit' => $type !== 'direct_sale' ? $basePrice * 0.1 : null,
            'current_highest_bid' => null,
            'highest_bidder_id' => null,
            'current_winner_id' => null,
            'start_time' => $type !== 'direct_sale' ? now()->addHours(1) : null,
            'end_time' => $type !== 'direct_sale' ? now()->addDays(7) : null,
            'finalization_deadline' => null,
            
            // Direct sale fields
            'price' => $type !== 'auction' ? fake()->randomFloat(2, 10000, 500000) : null,
            'stock' => $type !== 'auction' ? fake()->numberBetween(1, 100) : null,
            'low_stock_threshold' => $type !== 'auction' ? 5 : null,
            
            'status' => 'pending',
        ];
    }
    
    public function auction(): static
    {
        $basePrice = fake()->randomFloat(2, 10000, 1000000);
        
        return $this->state(fn (array $attributes) => [
            'type' => 'auction',
            'base_price' => $basePrice,
            'required_deposit' => $basePrice * 0.1,
            'current_highest_bid' => null,
            'highest_bidder_id' => null,
            'current_winner_id' => null,
            'start_time' => now()->addHours(1),
            'end_time' => now()->addDays(7),
            'finalization_deadline' => null,
            'price' => null,
            'stock' => null,
            'low_stock_threshold' => null,
        ]);
    }
    
    public function directSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'direct_sale',
            'price' => fake()->randomFloat(2, 10000, 500000),
            'stock' => fake()->numberBetween(1, 100),
            'low_stock_threshold' => 5,
            'base_price' => null,
            'required_deposit' => null,
            'start_time' => null,
            'end_time' => null,
        ]);
    }
    
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

class BidFactory extends Factory
{
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory()->auction(),
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 100000, 1000000),
        ];
    }
}

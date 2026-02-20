<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuctionParticipationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory()->auction(),
            'user_id' => User::factory(),
            'deposit_amount' => fake()->randomFloat(2, 10000, 100000),
            'deposit_status' => 'paid',
        ];
    }
}

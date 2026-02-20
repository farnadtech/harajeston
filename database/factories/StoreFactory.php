<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $storeName = $this->faker->company();
        
        return [
            'user_id' => User::factory(),
            'store_name' => $storeName,
            'slug' => Str::slug($storeName) . '-' . $this->faker->unique()->numberBetween(1, 10000),
            'description' => $this->faker->paragraph(),
            'banner_image' => null,
            'logo_image' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withImages(): static
    {
        return $this->state(fn (array $attributes) => [
            'banner_image' => 'stores/banners/test-banner.jpg',
            'logo_image' => 'stores/logos/test-logo.jpg',
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Enums\ServicesEnum;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(array_column(ServicesEnum::cases(), 'value')),
            'access_token' => fake()->sha256(),
            'refresh_token' => fake()->sha256(),
            'is_available' => true,
            'is_active' => true,
        ];
    }
}

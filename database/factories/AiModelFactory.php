<?php

namespace Database\Factories;

use App\Enums\ModelsEnum;
use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiModel>
 */
class AiModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(array_column(ModelsEnum::cases(), 'value')),
            'api_key' => fake()->uuid(),
            'persona' => fake()->paragraph(),
            'tone' => fake()->randomElement(['formal', 'casual', 'friendly', 'professional']),
            'is_multi_language' => fake()->boolean(),
            'is_auto_language' => fake()->boolean(),
            'is_active' => true,
            'is_available' => true,
        ];
    }
}

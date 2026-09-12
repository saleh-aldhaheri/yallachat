<?php

namespace Database\Factories;

use App\Models\ScheduledMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScheduledMessage>
 */
class ScheduledMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'run_at' => fake()->time('H:i:s'),
            'is_active' => fake()->boolean(),
        ];
    }
}

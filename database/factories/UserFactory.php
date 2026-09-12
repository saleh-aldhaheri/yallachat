<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            if (fake()->boolean(70)) {
                $name = urlencode($user->name);
                $user->addMediaFromUrl("https://ui-avatars.com/api/?name={$name}&background=random&size=200")
                    ->toMediaCollection('avatar');
            }
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withAvatar(): static
    {
        return $this->afterCreating(function (User $user) {
            $name = urlencode($user->name);
            $user->addMediaFromUrl("https://ui-avatars.com/api/?name={$name}&background=random&size=200")
                ->toMediaCollection('avatar');
        });
    }
}

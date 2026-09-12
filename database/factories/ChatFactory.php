<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['private', 'group']);

        return [
            'type' => $type,
            'name' => $type === 'group' ? fake()->words(3, true) : null,
            'created_by' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Chat $chat) {
            $otherUsers = User::where('id', '!=', $chat->created_by)
                ->inRandomOrder()
                ->take(fake()->numberBetween(1, 4))
                ->pluck('id');

            $chat->participants()->attach(
                $otherUsers->prepend($chat->created_by)->unique()->all(),
            );

            $participantIds = $chat->participants()->pluck('users.id');

            foreach (range(1, fake()->numberBetween(0, 10)) as $i) {
                Message::factory()->create([
                    'chat_id' => $chat->id,
                    'sender_id' => $participantIds->random(),
                ]);
            }
        });
    }
}

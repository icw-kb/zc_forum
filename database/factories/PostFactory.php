<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'thread_id' => \App\Models\Thread::inRandomOrder()->first()?->id ?? \App\Models\Thread::factory(),
            'forum_id' => function (array $attributes) {
                return \App\Models\Thread::find($attributes['thread_id'])?->forum_id;
            },
            'status' => 'open',
        ];
    }
}

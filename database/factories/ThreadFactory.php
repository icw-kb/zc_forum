<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . $this->faker->randomNumber(4),
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'forum_id' => \App\Models\Forum::inRandomOrder()->first()?->id ?? \App\Models\Forum::factory(),
            'status' => 'open',
            'views' => $this->faker->numberBetween(0, 1000),
        ];
    }
}

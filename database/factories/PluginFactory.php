<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plugin>
 */
class PluginFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'github_url' => fake()->optional(0.7)->url(),
            'status' => fake()->randomElement(['open', 'closed', 'locked', 'hidden']),
            'view_count' => fake()->numberBetween(0, 1000),
            'download_count' => fake()->numberBetween(0, 500),
            'featured' => fake()->boolean(20), // 20% chance of being featured
            'plugin_group_id' => \App\Models\PluginGroup::factory(),
        ];
    }

    /**
     * Indicate that the plugin is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * Indicate that the plugin is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    /**
     * Indicate that the plugin is popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => fake()->numberBetween(500, 2000),
            'download_count' => fake()->numberBetween(100, 800),
        ]);
    }
}

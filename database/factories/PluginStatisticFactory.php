<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PluginStatistic>
 */
class PluginStatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => fake()->randomElement(['view', 'download']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate that the statistic is a view.
     */
    public function view(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'view',
        ]);
    }

    /**
     * Indicate that the statistic is a download.
     */
    public function download(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'download',
        ]);
    }
}

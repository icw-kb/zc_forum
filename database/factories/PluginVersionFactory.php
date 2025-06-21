<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PluginVersion>
 */
class PluginVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version' => fake()->numberBetween(1, 9).'.'.fake()->numberBetween(0, 9).'.'.fake()->numberBetween(0, 9),
            'description' => fake()->sentence(),
            'file_path' => null, // Will be set if needed for download tests
            'file_size' => null,
            'file_hash' => null,
            'user_id' => \App\Models\User::factory(),
            'plugin_id' => \App\Models\Plugin::factory(),
            'status' => 'open',
            'is_encapsulated' => false,
            'count' => 0,
            'vc_url' => fake()->optional(0.7)->url(),
            'php_version' => fake()->optional(0.5)->randomElement(['7.4', '8.0', '8.1', '8.2', '8.3']),
        ];
    }

    /**
     * Indicate that the version has a file.
     */
    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => 'plugins/'.fake()->uuid().'/plugin.zip',
            'file_size' => fake()->numberBetween(1024, 1048576), // 1KB to 1MB
            'file_hash' => hash('sha256', fake()->text()),
        ]);
    }
}

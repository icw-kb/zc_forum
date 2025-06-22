<?php

namespace Database\Factories;

use App\Models\ZencartVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZencartVersion>
 */
class ZencartVersionFactory extends Factory
{
    protected $model = ZencartVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $versions = ['1.5.8', '1.5.7', '1.5.6', '1.5.5', '1.5.4', '1.5.3', '1.5.2', '1.5.1', '1.5.0'];
        
        return [
            'version' => $this->faker->randomElement($versions),
        ];
    }
}
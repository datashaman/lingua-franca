<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bot>
 */
class BotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locales = config('testing.locales');

        return [
            'name' => fake()->name(),
            'handle' => fake()->unique()->userName(),
            'description' => fake()->sentence(),
            'instructions' => fake()->paragraph(),
            'locale' => fake()->randomElement($locales),
            'properties' => [],
        ];
    }
}

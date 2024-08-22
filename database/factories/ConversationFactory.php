<?php

namespace Database\Factories;

use App\Enums\ConversationType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'description' => fake()->sentence(),
            'color' => fake()->hexColor(),
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => fake()->randomElement(ConversationType::class),
        ];
    }
}

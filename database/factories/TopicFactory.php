<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(),
            'user_id' => fake()->randomDigit(),
            'description' => fake()->paragraph(),
            'discussion_id' => fake()->randomDigit(),
            'comments_counter' => fake()->randomDigit(),
            'last_update' => fake()->date(),
        ];
    }
}

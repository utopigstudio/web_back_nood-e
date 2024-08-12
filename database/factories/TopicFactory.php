<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Organization;
use App\Models\User;
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
            'user_id' => User::factory()->create()->id,
            'description' => fake()->paragraph(),
            'discussion_id' => Discussion::factory()->create()->id,
            'comments_counter' => fake()->randomDigit(),
            'last_update' => fake()->date(),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'user_id' => fake()->randomDigit(),
            'discussion_id' => fake()->randomDigit(),
        ];
    }
}

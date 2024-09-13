<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'surname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'description' => fake()->sentence(),
            'password' => 'password',
            'organization_id' => rand(1, 10),
            'image' => fake()->imageUrl(),
            'invite_accepted_at' => fake()->dateTime(),
            'role_id' => 1,
        ];
    }
}

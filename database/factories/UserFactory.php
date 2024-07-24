<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'email_verified_at' => now(),
            // 'password' => static::$password ??= Hash::make('password'),
            'password' => 'password',
            'role_id' => rand(1, 2),
            'organization_id' => rand(1, 15),
            'domain' => fake()->domainName(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRolesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->name(),
            'role_id' => rand(0, 4),
        ];
    }
}

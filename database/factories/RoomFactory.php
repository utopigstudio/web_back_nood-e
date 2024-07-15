<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'floor' => fake()->randomNumber(2),
            'building' => fake()->name(),
            'capacity' => fake()->randomNumber(2),
            'status' => rand(0, 1),
        ];
    }
}

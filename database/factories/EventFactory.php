<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'description' => fake()->paragraph(),
            'start' => fake()->dateTimeBetween($startDate = '-1 days', $endDate = '1 days', $timezone = null),
            'end' => fake()->dateTimeBetween($startDate = '-1 days', $endDate = '7 days', $timezone = null),
            'room_id' => fake()->randomDigit(),
            'price' => fake()->randomFloat(2, 1, 100),
            'image' => fake()->imageUrl(),
            'link' => fake()->url()
        ];
    }
}

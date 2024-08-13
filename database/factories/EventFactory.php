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
            'image' => fake()->imageUrl(),
            'date' => fake()->date()-min('now'),
            'start' => fake()->dateTimeBetween($startDate = '-1 days', $endDate = '1 days', $timezone = null),
            'end' => fake()->dateTimeBetween($startDate = '-1 days', $endDate = '7 days', $timezone = null),
            'meet_link' => fake()->url(),
            'room_id' => fake()->randomDigit(),
            'user_id' => fake()->randomDigit() 
        ];
    }
}

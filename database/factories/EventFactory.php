<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'description' => fake()->paragraph(),
            'start' => fake()->dateTimeBetween($startDate = '-1 days', $endDate = '1 days', $timezone = null),
            'end' => fake()->dateTimeBetween($startDate = '-1 days', $endDate = '7 days', $timezone = null),
            'meet_link' => fake()->url(),
            'room_id' => rand(1, 10),
            'author_id' => rand(1, 10), 
        ];
    }
}

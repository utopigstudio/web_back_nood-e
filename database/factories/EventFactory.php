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
        $start = fake()->dateTimeBetween($startDate = '-10 days', $endDate = '10 days', $timezone = null);
        $end = (new \Carbon\Carbon($start))->addHours(rand(1, 5));

        return [
            'title' => fake()->name(),
            'description' => fake()->paragraph(),
            'start' => $start,
            'end' => $end,
            'meet_link' => fake()->url(),
            'room_id' => rand(1, 10),
            'author_id' => rand(1, 10), 
        ];
    }
}

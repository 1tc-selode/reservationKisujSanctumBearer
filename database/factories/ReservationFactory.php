<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id' => User::inRandomOrder()->first()->id,
        'reservation_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
        'guests' => $this->faker->NumberBetween(1,10),
        'note'=>$this->faker->sentence()
    ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $checkIn = fake()->dateTimeBetween('+1 day', '+10 days');
        $checkOut = (clone $checkIn)->modify('+'.fake()->numberBetween(1, 5).' days');

        return [
            'reservation_number' => 'RES'.fake()->unique()->numerify('##########'),
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'check_in_date' => $checkIn->format('Y-m-d'),
            'check_out_date' => $checkOut->format('Y-m-d'),
            'number_of_adults' => fake()->numberBetween(1, 3),
            'number_of_children' => 0,
            'total_price' => fake()->numberBetween(50000, 500000),
            'status' => 'confirmed',
            'special_requests' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\RoomType;
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
    public function definition(): array
    {
        return [
            'room_number' => (string) fake()->unique()->numberBetween(100, 9999),
            'room_type_id' => RoomType::factory(),
            'floor' => fake()->numberBetween(0, 10),
            'price_per_night' => fake()->numberBetween(30000, 300000),
            'max_occupancy' => fake()->numberBetween(1, 6),
            'status' => 'available',
        ];
    }
}

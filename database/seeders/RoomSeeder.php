<?php
// database/seeders/RoomSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = RoomType::all();

        // Chambres Standard (étages 1-2)
        $standardType = $roomTypes->where('name', 'Standard')->first();
        for ($floor = 1; $floor <= 2; $floor++) {
            for ($room = 1; $room <= 10; $room++) {
                $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                Room::updateOrCreate(
                    ['room_number' => $roomNumber],
                    [
                        'room_type_id' => $standardType->id,
                        'floor' => $floor,
                        'price_per_night' => 50000,
                        'max_occupancy' => 2,
                        'status' => $this->getRandomStatus(),
                    ]
                );
            }
        }

        // Chambres Deluxe (étages 3-4)
        $deluxeType = $roomTypes->where('name', 'Deluxe')->first();
        for ($floor = 3; $floor <= 4; $floor++) {
            for ($room = 1; $room <= 8; $room++) {
                $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                Room::updateOrCreate(
                    ['room_number' => $roomNumber],
                    [
                        'room_type_id' => $deluxeType->id,
                        'floor' => $floor,
                        'price_per_night' => 85000,
                        'max_occupancy' => 3,
                        'status' => $this->getRandomStatus(),
                    ]
                );
            }
        }

        // Suites Junior (étage 5)
        $juniorType = $roomTypes->where('name', 'Suite Junior')->first();
        for ($room = 1; $room <= 6; $room++) {
            $roomNumber = '5' . str_pad($room, 2, '0', STR_PAD_LEFT);
            Room::updateOrCreate(
                ['room_number' => $roomNumber],
                [
                    'room_type_id' => $juniorType->id,
                    'floor' => 5,
                    'price_per_night' => 120000,
                    'max_occupancy' => 3,
                    'status' => $this->getRandomStatus(),
                ]
            );
        }

        // Suite Présidentielle (étage 5)
        $presidentialType = $roomTypes->where('name', 'Suite Présidentielle')->first();
        Room::updateOrCreate(
            ['room_number' => '501'],
            [
                'room_type_id' => $presidentialType->id,
                'floor' => 5,
                'price_per_night' => 250000,
                'max_occupancy' => 4,
                'status' => 'available',
            ]
        );

        // Chambres Familiales (étage 2)
        $familyType = $roomTypes->where('name', 'Familiale')->first();
        for ($room = 1; $room <= 4; $room++) {
            $roomNumber = '2' . str_pad($room + 10, 2, '0', STR_PAD_LEFT);
            Room::updateOrCreate(
                ['room_number' => $roomNumber],
                [
                    'room_type_id' => $familyType->id,
                    'floor' => 2,
                    'price_per_night' => 150000,
                    'max_occupancy' => 5,
                    'status' => $this->getRandomStatus(),
                ]
            );
        }

        $this->command->info('Chambres créées avec succès !');
    }

    private function getRandomStatus()
    {
        $statuses = ['available', 'occupied', 'maintenance'];
        $weights = [70, 25, 5]; // 70% disponibles, 25% occupées, 5% maintenance
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $index => $status) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $status;
            }
        }
        
        return 'available';
    }
}
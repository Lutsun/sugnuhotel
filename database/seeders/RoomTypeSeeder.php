<?php
// database/seeders/RoomTypeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Standard',
                'description' => 'Chambre confortable avec lit double, idéale pour les voyageurs seuls ou les couples',
                'base_price' => 50000,
                'max_occupancy' => 2,
                'image' => 'standard.jpg',
            ],
            [
                'name' => 'Deluxe',
                'description' => 'Chambre spacieuse avec vue sur la ville, literie haut de gamme et salle de bain luxueuse',
                'base_price' => 85000,
                'max_occupancy' => 3,
                'image' => 'deluxe.jpg',
            ],
            [
                'name' => 'Suite Junior',
                'description' => 'Suite élégante avec coin salon séparé et vue panoramique',
                'base_price' => 120000,
                'max_occupancy' => 3,
                'image' => 'junior-suite.jpg',
            ],
            [
                'name' => 'Suite Présidentielle',
                'description' => 'Suite luxueuse avec chambre séparée, salon, salle à manger et terrasse privée',
                'base_price' => 250000,
                'max_occupancy' => 4,
                'image' => 'presidential.jpg',
            ],
            [
                'name' => 'Familiale',
                'description' => 'Grande chambre communicante idéale pour les familles',
                'base_price' => 150000,
                'max_occupancy' => 5,
                'image' => 'family.jpg',
            ],
        ];

        foreach ($roomTypes as $type) {
            RoomType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('Types de chambres créés avec succès !');
    }
}
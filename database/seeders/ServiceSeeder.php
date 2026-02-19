<?php
// database/seeders/ServiceSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Petit-déjeuner',
                'description' => 'Buffet petit-déjeuner complet avec produits locaux et internationaux',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Dîner',
                'description' => 'Dîner gastronomique au restaurant de l\'hôtel',
                'price' => 15000,
                'is_active' => true,
            ],
            [
                'name' => 'Parking',
                'description' => 'Place de parking sécurisée',
                'price' => 2000,
                'is_active' => true,
            ],
            [
                'name' => 'Spa',
                'description' => 'Accès au spa et massage d\'une heure',
                'price' => 25000,
                'is_active' => true,
            ],
            [
                'name' => 'Navette aéroport',
                'description' => 'Transfert aller-retour aéroport',
                'price' => 15000,
                'is_active' => true,
            ],
            [
                'name' => 'Lit bébé',
                'description' => 'Lit bébé avec équipement complet',
                'price' => 3000,
                'is_active' => true,
            ],
            [
                'name' => 'Petit-déjeuner en chambre',
                'description' => 'Petit-déjeuner servi dans votre chambre',
                'price' => 6000,
                'is_active' => true,
            ],
            [
                'name' => 'Visite guidée',
                'description' => 'Visite guidée de Dakar (demi-journée)',
                'price' => 20000,
                'is_active' => true,
            ],
            [
                'name' => 'Location de voiture',
                'description' => 'Location de voiture avec chauffeur',
                'price' => 35000,
                'is_active' => false, // Service temporairement indisponible
            ],
            [
                'name' => 'Blanchisserie',
                'description' => 'Service de nettoyage et repassage',
                'price' => 5000,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }

        $this->command->info('Services créés avec succès !');
    }
}
<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà pour éviter les doublons
        if (!User::where('email', 'admin@sugnuhotel.com')->exists()) {
            User::create([
                'name' => 'Admin Principal',
                'email' => 'admin@sugnuhotel.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '+221 77 720 31 62',
                'address' => 'Dakar, Sénégal',
                'email_verified_at' => now(),
            ]);
        }

        // Créer un réceptionniste
        if (!User::where('email', 'reception@sugnuhotel.com')->exists()) {
            User::create([
                'name' => 'Fatou Diop',
                'email' => 'reception@sugnuhotel.com',
                'password' => Hash::make('recept123'),
                'role' => 'receptionist',
                'phone' => '+221 78 123 45 67',
                'address' => 'Dakar, Sénégal',
                'email_verified_at' => now(),
            ]);
        }

        // Créer un client test
        if (!User::where('email', 'client@test.com')->exists()) {
            User::create([
                'name' => 'Client Test',
                'email' => 'client@test.com',
                'password' => Hash::make('client123'),
                'role' => 'client',
                'phone' => '+221 76 123 45 67',
                'address' => 'Thiès, Sénégal',
                'email_verified_at' => now(),
            ]);
        }

        // Créer quelques clients supplémentaires pour les tests
        $clients = [
            [
                'name' => 'Mamadou Ndiaye',
                'email' => 'mamadou.ndiaye@email.com',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'phone' => '+221 77 234 56 78',
                'address' => 'Saint-Louis, Sénégal',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($clients as $client) {
            if (!User::where('email', $client['email'])->exists()) {
                User::create([
                    'name' => $client['name'],
                    'email' => $client['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'phone' => $client['phone'],
                    'address' => $client['address'],
                    'email_verified_at' => now(),
                ]);
            }
        }
    }
}
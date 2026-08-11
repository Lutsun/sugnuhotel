<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ServiceSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
        ]);

        $this->command->info('Tous les seeders ont été exécutés avec succès !');
    }
}
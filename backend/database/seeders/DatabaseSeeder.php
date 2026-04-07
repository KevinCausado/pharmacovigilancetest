<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters — orders depend on customers and medications existing first
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            MedicationSeeder::class,
            OrderSeeder::class,
        ]);
    }
}

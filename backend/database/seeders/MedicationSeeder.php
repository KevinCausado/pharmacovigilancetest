<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        Medication::create([
            'name'            => 'Amoxicillin 500mg',
            'lot_number'      => '951357',
            'description'     => 'Antibiotic compound — possible contamination detected',
            'expiration_date' => '2026-12-31',
        ]);

        Medication::create([
            'name'            => 'Ibuprofen 400mg',
            'lot_number'      => '123456',
            'description'     => 'Anti-inflammatory',
            'expiration_date' => '2027-06-30',
        ]);

        Medication::create([
            'name'            => 'Metformin 850mg',
            'lot_number'      => '789012',
            'description'     => 'Diabetes medication',
            'expiration_date' => '2027-03-15',
        ]);
    }
}

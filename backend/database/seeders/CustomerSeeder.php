<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Jennifer Arnold',  'email' => 'jennifer.arnold@email.com',  'phone' => '555-0101'],
            ['name' => 'Michael Thompson', 'email' => 'michael.thompson@email.com', 'phone' => '555-0102'],
            ['name' => 'Sarah Williams',   'email' => 'sarah.williams@email.com',   'phone' => '555-0103'],
            ['name' => 'Robert Davis',     'email' => 'robert.davis@email.com',     'phone' => '555-0104'],
            ['name' => 'Emily Martinez',   'email' => 'emily.martinez@email.com',   'phone' => '555-0105'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}

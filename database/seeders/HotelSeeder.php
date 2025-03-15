<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Luxury Hotel',
                'address' => '123 Luxury St',
                'city' => 'Luxury City',
                'country' => 'Luxland',
                'description' => 'A luxurious hotel with all amenities.',
                'coordinate' => '40.7128,-74.0060',
                'owner_id' => 2, 
            ],
            [
                'name' => 'Budget Inn',
                'address' => '456 Budget Ave',
                'city' => 'Budget City',
                'country' => 'Budgetland',
                'description' => 'An affordable place to stay.',
                'coordinate' => '34.0522,-118.2437',
                'owner_id' => 2, 
            ],
            [
                'name' => 'Family Resort',
                'address' => '789 Family Blvd',
                'city' => 'Family City',
                'country' => 'Familyland',
                'description' => 'A family-friendly resort with activities for all ages.',
                'coordinate' => '51.5074,-0.1278',
                'owner_id' => 2, 
            ],
        ];

        foreach ($hotels as $hotel) {
            DB::table('hotels')->insert([
                'name' => $hotel['name'],
                'address' => $hotel['address'],
                'city' => $hotel['city'],
                'country' => $hotel['country'],
                'description' => $hotel['description'],
                'coordinate' => $hotel['coordinate'],
                'owner_id' => $hotel['owner_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
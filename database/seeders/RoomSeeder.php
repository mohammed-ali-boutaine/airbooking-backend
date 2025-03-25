<?php

namespace Database\Seeders;
use App\Models\Room;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Generate 10 rooms for Hotel ID 1
         Room::factory()->count(10)->create([
            'hotel_id' => 1,
        ]);

        // Generate 10 rooms for Hotel ID 2
        Room::factory()->count(10)->create([
            'hotel_id' => 2,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Room;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Client;
use App\Models\RoomImage;
use Illuminate\Database\Seeder;
use Database\Seeders\HotelSeeder;
use Database\Seeders\TagsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // users seeding for clietns owners and one admin
        $this->call(UsersSeeder::class);



    // Create rooms
    Room::factory(10)->create()->each(function ($room) {
        RoomImage::factory(3)->create(['room_id' => $room->id]); // 3 images per room
    });
        // $this->call([
        //     RoomSeeder::class,
        // ]);
        
       

        // $this->call(HotelSeeder::class);
        // $this->call(TagsSeeder::class);
        // $this->call(HotelTagSeeder::class);
    }
}

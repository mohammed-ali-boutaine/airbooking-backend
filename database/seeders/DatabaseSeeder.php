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
    // Create rooms
    Room::factory(10)->create()->each(function ($room) {
        RoomImage::factory(3)->create(['room_id' => $room->id]); // 3 images per room
    });
        // $this->call([
        //     RoomSeeder::class,
        // ]);
        
        // $client = new Client([
        //     'name' => 'John Doe',
        //     'email' => 'john@example.com',
        //     'password' => bcrypt('password')
        // ]);
        // $client->save();
        // $owner = new Owner([
        //     'name' => 'Hotel Owner',
        //     'email' => 'owner@example.com',
        //     'password' => bcrypt('password')
        // ]);
        // $owner->save();
        // $admin = new Admin([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password')
        // ]);
        // $admin->save();

        // $this->call(HotelSeeder::class);
        // $this->call(TagsSeeder::class);
        // $this->call(HotelTagSeeder::class);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Client;
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


        
        $client = new Client([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password')
        ]);
        $client->save();
        $owner = new Owner([
            'name' => 'Hotel Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password')
        ]);
        $owner->save();
        $admin = new Admin([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);
        $admin->save();

        $this->call(HotelSeeder::class);
        $this->call(TagsSeeder::class);
        $this->call(HotelTagSeeder::class);
    }
}

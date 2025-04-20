<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Tag;
use App\Models\Room;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some tags first
        $tags = Tag::factory()->count(10)->create();

        // Create some users to be hotel owners
        $owners = User::factory()->count(5)->create();

        // Create 20 hotels with relationships
        Hotel::factory()
            ->count(20)
            ->recycle($owners)
            ->has(Review::factory()->count(5))
            ->has(Room::factory()->count(rand(3, 10)))
            ->create()
            ->each(function (Hotel $hotel) use ($tags) {
                // Attach random tags to each hotel
                $hotel->tags()->attach(
                    $tags->random(rand(2, 5))->pluck('id')->toArray()
                );
            });
    }
}

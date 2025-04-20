<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id'          => $this->id,
            'name'        => $this->name,
            'address'     => $this->address,
            'city'        => $this->city,
            'country'     => $this->country,
            'description' => $this->description,



            'profile_image' => $this->profile_path
                ? asset('storage/' . $this->profile_path)
                : asset('images/default-hotel.jpg'), // fallback

            'cover_image' => $this->cover_path
                ? asset('storage/' . $this->cover_path)
                : asset('images/default-cover.jpg'),

                'coordinate'  => json_decode($this->coordinate, true), // return as array

            'owner' => [
                'id'   => $this->owner->id,
                'name' => $this->owner->name,
                'email' => $this->owner->email,
            ],

            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];

    
    }
}

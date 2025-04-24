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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'profile_path' => $this->profile_path ? asset('storage/' . $this->profile_path) : null,
            'cover_path' => $this->cover_path ? asset('storage/' . $this->cover_path) : null,
            'coordinate' => json_decode($this->coordinate),
            'owner_id' => $this->owner_id,
            'owner' => $this->whenLoaded('owner'),
            'tags' => $this->whenLoaded('tags'),
            'rooms' => $this->whenLoaded('rooms'),
            'reviews' => $this->whenLoaded('reviews'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /*
    
    Basic: WiFi, TV, Air Conditioning, Heating

    Comfort: Mini Bar, Coffee Maker, Balcony, Ocean View

    Luxury: Jacuzzi, Private Pool, Butler Service
    
    */
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'bed_numbers',
        'number_of_guests',
        'price_per_night',
        'is_available',
        'amenities',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'amenities' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}

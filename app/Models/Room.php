<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{

    use HasFactory;
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
        'capacity',
        'price_per_night',
        'is_available',
        'amenities',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'amenities' => 'array',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function booking(){
        return $this->hasMany(Booking::class);
    }

    public function isAvailbale(){

    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //

    protected $fillable = ['room_id', 'user_id', 'check_in', 'check_out', 'total_price'];

    public function room()
    {
        return $this->hasOne(Room::class);
    }


    public function client()
    {
        return $this->hasOne(Client::class);
    }
}

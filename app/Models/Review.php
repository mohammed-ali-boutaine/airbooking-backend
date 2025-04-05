<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //

    protected $fillable = ['hotel_id', 'user_id', 'rating', 'comment'];

    public function hotel()
    {
        return $this->hasOne(Hotel::class);
    }
}

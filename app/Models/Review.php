<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    //
    use HasFactory;

    protected $fillable = ['hotel_id', 'user_id', 'rating', 'comment'];

    public function hotel()
    {
        return $this->hasOne(Hotel::class);
    }
}

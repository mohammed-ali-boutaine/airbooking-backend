<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    //
    protected $table = 'hotels';


    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'country',
        'profile_path',
        'cover_path',
        'coordinate',
        'owner_id'
    ];


    public function owener(){
        return $this->hasOne(Owner::class);
    }


    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}

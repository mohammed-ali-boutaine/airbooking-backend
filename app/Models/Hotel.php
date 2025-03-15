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
        'address',
        'city',
        'country',
        'description',
        'profile_path',
        'cover_path',
        'coordinate',
        'owner_id'
    ];
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}

<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    //


    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}

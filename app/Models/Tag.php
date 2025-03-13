<?php

namespace App\Models;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    //
    protected $fillable = ['name', 'icon_path'];


    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class);
    }
}

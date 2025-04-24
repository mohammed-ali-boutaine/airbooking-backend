<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    // Use SoftDeletes if you uncomment the softDeletes line in your migration
    // use SoftDeletes;

    protected $fillable = [
        'room_id',
        'client_id',
        'check_in',
        'check_out',
        'number_of_guests',
        'total_price',
        'status'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'decimal:2',
        'number_of_guests' => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}

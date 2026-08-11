<?php
// app/Models/RoomType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'max_occupancy',
        'image'
    ];

    /**
     * Relation avec les chambres
     * Un type de chambre peut avoir plusieurs chambres
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relation avec les réservations via les chambres
     */
    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, Room::class);
    }
}
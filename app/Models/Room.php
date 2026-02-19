<?php
// app/Models/Room.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'price_per_night',
        'max_occupancy',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    /**
     * Relation avec le type de chambre
     * Une chambre appartient à un type
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Relation avec les réservations
     * Une chambre peut avoir plusieurs réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relation avec les images
     * Une chambre peut avoir plusieurs images
     */
    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    /**
     * Scope pour les chambres disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope pour les chambres occupées
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }
}
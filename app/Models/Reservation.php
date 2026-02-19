<?php
// app/Models/Reservation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'number_of_adults',
        'number_of_children',
        'total_price',
        'status',
        'special_requests'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2'
    ];

    /**
     * Relation avec l'utilisateur
     * Une réservation appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la chambre
     * Une réservation appartient à une chambre
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relation avec les services
     * Une réservation peut avoir plusieurs services
     */
    public function services()
    {
        return $this->hasMany(ReservationService::class);
    }

    /**
     * Scope pour les réservations actives
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }
}
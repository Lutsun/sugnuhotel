<?php
// app/Models/Room.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // IMPORTANT: Ajoutez cette ligne en haut

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
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Relation avec les réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relation avec les images
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

    /**
     * Vérifier si la chambre est disponible pour une période donnée
     * 
     * @param string|\Carbon\Carbon $checkIn Date d'arrivée
     * @param string|\Carbon\Carbon $checkOut Date de départ
     * @return bool
     */
    public function isAvailable($checkIn, $checkOut)
    {
        // Convertir en objets Carbon si ce sont des strings
        if (!$checkIn instanceof Carbon) {
            $checkIn = Carbon::parse($checkIn);
        }
        if (!$checkOut instanceof Carbon) {
            $checkOut = Carbon::parse($checkOut);
        }
        
        // Vérifier les réservations existantes qui chevauchent la période
        $existingReservations = $this->reservations()
            ->whereIn('status', ['confirmed', 'checked_in']) // Statuts qui occupent la chambre
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->count();
        
        return $existingReservations === 0;
    }

    /**
     * Obtenir le prix total pour un nombre de nuits
     */
    public function getPriceForNights($nights)
    {
        return $this->price_per_night * $nights;
    }

    /**
     * Vérifie la disponibilité d'une chambre pour une période donnée, à partir de son id.
     * Version centralisée utilisée par l'API (remplace les 3 implémentations dupliquées
     * trouvées dans BookingController, Reception\ReservationController et Room::isAvailable).
     *
     * @param  string|\Carbon\Carbon  $checkIn
     * @param  string|\Carbon\Carbon  $checkOut
     * @param  int|null  $excludeReservationId  Ignore cette réservation (utile lors d'une modification)
     */
    public static function isAvailableBetween(int $roomId, $checkIn, $checkOut, ?int $excludeReservationId = null): bool
    {
        $checkIn = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
        $checkOut = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);

        $conflicting = Reservation::where('room_id', $roomId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->when($excludeReservationId, fn ($query) => $query->where('id', '!=', $excludeReservationId))
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->exists();

        return ! $conflicting;
    }
}
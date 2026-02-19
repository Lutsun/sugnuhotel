<?php
// app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    /**
     * Relation avec les réservations via la table pivot
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_services')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
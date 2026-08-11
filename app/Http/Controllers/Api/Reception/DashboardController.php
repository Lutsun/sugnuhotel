<?php

namespace App\Http\Controllers\Api\Reception;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayArrivals = Reservation::with(['user', 'room'])
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get();

        $todayDepartures = Reservation::with(['user', 'room'])
            ->whereDate('check_out_date', $today)
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->get();

        $currentGuests = Reservation::with(['user', 'room'])
            ->where('status', 'checked_in')
            ->get();

        $upcomingArrivals = Reservation::with(['user', 'room'])
            ->whereBetween('check_in_date', [$today, $today->copy()->addDays(7)])
            ->where('status', 'confirmed')
            ->orderBy('check_in_date')
            ->get();

        return response()->json([
            'rooms' => [
                'total' => Room::count(),
                'available' => Room::where('status', 'available')->count(),
                'occupied' => Room::where('status', 'occupied')->count(),
            ],
            'today_arrivals' => ReservationResource::collection($todayArrivals),
            'today_departures' => ReservationResource::collection($todayDepartures),
            'current_guests' => ReservationResource::collection($currentGuests),
            'upcoming_arrivals' => ReservationResource::collection($upcomingArrivals),
        ]);
    }

    /**
     * Événements pour le calendrier (FullCalendar côté Angular). Renvoie l'id de réservation
     * plutôt qu'une URL Blade (corrige le lien mort vers reception.reservations.show).
     */
    public function calendar()
    {
        $reservations = Reservation::with(['room', 'user'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get()
            ->map(fn (Reservation $reservation) => [
                'id' => $reservation->id,
                'title' => 'Chambre '.$reservation->room->room_number.' - '.$reservation->user->name,
                'start' => $reservation->check_in_date->format('Y-m-d'),
                'end' => $reservation->check_out_date->format('Y-m-d'),
                'backgroundColor' => $this->getEventColor($reservation->status),
                'borderColor' => $this->getEventColor($reservation->status),
            ]);

        return response()->json($reservations);
    }

    private function getEventColor(string $status): string
    {
        return match ($status) {
            'confirmed' => '#28a745',
            'checked_in' => '#007bff',
            'pending' => '#ffc107',
            default => '#6c757d',
        };
    }
}

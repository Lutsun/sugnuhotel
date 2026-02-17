<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Arrivées du jour
        $todayArrivals = Reservation::with(['user', 'room'])
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get();
        
        // Départs du jour
        $todayDepartures = Reservation::with(['user', 'room'])
            ->whereDate('check_out_date', $today)
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->get();
        
        // Réservations en cours (checked_in)
        $currentGuests = Reservation::with(['user', 'room'])
            ->where('status', 'checked_in')
            ->get();
        
        // Statistiques rapides
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $totalRooms = Room::count();
        
        // Prochaines arrivées (7 jours)
        $upcomingArrivals = Reservation::with(['user', 'room'])
            ->whereBetween('check_in_date', [$today, $today->copy()->addDays(7)])
            ->where('status', 'confirmed')
            ->orderBy('check_in_date')
            ->get();
        
        return view('reception.dashboard', compact(
            'todayArrivals',
            'todayDepartures',
            'currentGuests',
            'availableRooms',
            'occupiedRooms',
            'totalRooms',
            'upcomingArrivals'
        ));
    }

    public function calendar()
    {
        $reservations = Reservation::with(['room', 'user'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get()
            ->map(function ($reservation) {
                return [
                    'title' => 'Chambre ' . $reservation->room->room_number . ' - ' . $reservation->user->name,
                    'start' => $reservation->check_in_date,
                    'end' => $reservation->check_out_date,
                    'url' => route('reception.reservations.show', $reservation->id),
                    'backgroundColor' => $this->getEventColor($reservation->status),
                    'borderColor' => $this->getEventColor($reservation->status)
                ];
            });
        
        return view('reception.calendar', compact('reservations'));
    }

    private function getEventColor($status)
    {
        return match($status) {
            'confirmed' => '#28a745', // Vert
            'checked_in' => '#007bff', // Bleu
            'pending' => '#ffc107',    // Jaune
            default => '#6c757d'        // Gris
        };
    }
}
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        $today = Carbon::today();
        $todayArrivals = Reservation::whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        $todayDepartures = Reservation::whereDate('check_out_date', $today)
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->count();

        $recentReservations = Reservation::with(['user', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'rooms' => [
                'total' => $totalRooms,
                'available' => $availableRooms,
                'occupied' => $occupiedRooms,
                'maintenance' => $maintenanceRooms,
                'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0,
            ],
            'today' => [
                'arrivals' => $todayArrivals,
                'departures' => $todayDepartures,
            ],
            'reservations' => [
                'total' => Reservation::count(),
                'pending' => Reservation::where('status', 'pending')->count(),
                'confirmed' => Reservation::where('status', 'confirmed')->count(),
                'cancelled' => Reservation::where('status', 'cancelled')->count(),
            ],
            'revenue' => [
                'monthly' => (float) Reservation::whereMonth('created_at', Carbon::now()->month)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_price'),
                'total' => (float) Reservation::where('status', '!=', 'cancelled')->sum('total_price'),
            ],
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
            ],
            'recent_reservations' => ReservationResource::collection($recentReservations),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();
        
        // Réservations du jour
        $today = Carbon::today();
        $todayArrivals = Reservation::whereDate('check_in_date', $today)
                                    ->whereIn('status', ['confirmed', 'checked_in'])
                                    ->count();
        
        $todayDepartures = Reservation::whereDate('check_out_date', $today)
                                      ->whereIn('status', ['checked_in', 'confirmed'])
                                      ->count();
        
        // Statistiques de réservations
        $totalReservations = Reservation::count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $confirmedReservations = Reservation::where('status', 'confirmed')->count();
        $cancelledReservations = Reservation::where('status', 'cancelled')->count();
        
        // Chiffre d'affaires
        $monthlyRevenue = Reservation::whereMonth('created_at', Carbon::now()->month)
                                     ->where('status', '!=', 'cancelled')
                                     ->sum('total_price');
        
        $totalRevenue = Reservation::where('status', '!=', 'cancelled')
                                   ->sum('total_price');
        
        // Dernières réservations
        $recentReservations = Reservation::with(['user', 'room'])
                                         ->orderBy('created_at', 'desc')
                                         ->limit(10)
                                         ->get();
        
        // Taux d'occupation
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;
        
        // Statistiques utilisateurs
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();

        
        return view('admin.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'todayArrivals',
            'todayDepartures',
            'totalReservations',
            'pendingReservations',
            'confirmedReservations',
            'cancelledReservations',
            'monthlyRevenue',
            'totalRevenue',
            'recentReservations',
            'occupancyRate',
            'totalUsers',
            'newUsersThisMonth'
        ));
    }
}
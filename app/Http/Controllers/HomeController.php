<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Afficher la page d'accueil
     */
    public function index()
    {
        $roomTypes = RoomType::with('rooms')->get();
        $services = Service::where('is_active', true)->get();
        $rooms = Room::where('status', 'available')->limit(6)->get();
        
        return view('home', compact('roomTypes', 'services', 'rooms'));
    }

    /**
     * Afficher toutes les chambres
     */
    public function rooms(Request $request)
    {
        $query = Room::with('roomType')->where('status', 'available');
        
        // Filtres
        if ($request->has('capacity')) {
            $query->where('max_occupancy', '>=', $request->capacity);
        }
        
        if ($request->has('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }
        
        if ($request->has('price_min')) {
            $query->where('price_per_night', '>=', $request->price_min);
        }
        
        if ($request->has('price_max')) {
            $query->where('price_per_night', '<=', $request->price_max);
        }
        
        $rooms = $query->paginate(9);
        $roomTypes = RoomType::all();
        
        return view('rooms.index', compact('rooms', 'roomTypes'));
    }

    /**
     * Afficher le détail d'une chambre
     */
    public function showRoom($id)
    {
        $room = Room::with(['roomType', 'images'])->findOrFail($id);
        $similarRooms = Room::where('room_type_id', $room->room_type_id)
                            ->where('id', '!=', $id)
                            ->where('status', 'available')
                            ->limit(3)
                            ->get();
        
        return view('rooms.show', compact('room', 'similarRooms'));
    }
}
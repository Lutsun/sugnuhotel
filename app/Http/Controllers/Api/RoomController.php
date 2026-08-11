<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomTypeResource;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Liste publique des chambres disponibles, avec filtres (miroir de HomeController::rooms).
     */
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'images'])->where('status', 'available');

        if ($request->filled('capacity')) {
            $query->where('max_occupancy', '>=', $request->capacity);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        if ($request->filled('price_min')) {
            $query->where('price_per_night', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price_per_night', '<=', $request->price_max);
        }

        $rooms = $query->paginate(9);

        return RoomResource::collection($rooms);
    }

    public function show($id)
    {
        $room = Room::with(['roomType', 'images'])->findOrFail($id);

        $similarRooms = Room::with(['roomType', 'images'])
            ->where('room_type_id', $room->room_type_id)
            ->where('id', '!=', $id)
            ->where('status', 'available')
            ->limit(3)
            ->get();

        return response()->json([
            'room' => new RoomResource($room),
            'similar_rooms' => RoomResource::collection($similarRooms),
        ]);
    }

    public function roomTypes()
    {
        return RoomTypeResource::collection(RoomType::all());
    }
}

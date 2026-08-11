<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomTypeResource;
use App\Http\Resources\ServiceResource;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;

class HomeController extends Controller
{
    /**
     * Données de la page d'accueil publique (miroir de HomeController::index côté Blade).
     */
    public function index()
    {
        return response()->json([
            'room_types' => RoomTypeResource::collection(RoomType::with('rooms')->get()),
            'services' => ServiceResource::collection(Service::where('is_active', true)->get()),
            'rooms' => RoomResource::collection(Room::with(['roomType', 'images'])->where('status', 'available')->limit(6)->get()),
        ]);
    }

    /**
     * Services actifs, utilisés sur la page d'accueil et lors de la confirmation de réservation.
     */
    public function services()
    {
        return ServiceResource::collection(Service::where('is_active', true)->get());
    }
}

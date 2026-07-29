<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'images']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        return RoomResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:0|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:10',
            'status' => 'required|in:available,occupied,maintenance,out_of_service',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $room = Room::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $image->store('rooms', 'public'),
                ]);
            }
        }

        return new RoomResource($room->load(['roomType', 'images']));
    }

    public function show(Room $room)
    {
        return new RoomResource($room->load(['roomType', 'images']));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,'.$room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:0|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:10',
            'status' => 'required|in:available,occupied,maintenance,out_of_service',
        ]);

        $room->update($validated);

        return new RoomResource($room->load(['roomType', 'images']));
    }

    public function destroy(Room $room)
    {
        $hasFutureReservations = $room->reservations()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_in_date', '>', now())
            ->exists();

        if ($hasFutureReservations) {
            return response()->json([
                'message' => 'Impossible de supprimer cette chambre car elle a des réservations futures.',
            ], 422);
        }

        foreach ($room->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $room->delete();

        return response()->json(['message' => 'Chambre supprimée avec succès.']);
    }

    public function uploadImage(Request $request, Room $room)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $image = RoomImage::create([
            'room_id' => $room->id,
            'image_path' => $request->file('image')->store('rooms', 'public'),
        ]);

        return (new RoomResource($room->load(['roomType', 'images'])))
            ->additional(['uploaded_image_id' => $image->id]);
    }

    public function deleteImage(RoomImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['message' => 'Image supprimée avec succès.']);
    }
}

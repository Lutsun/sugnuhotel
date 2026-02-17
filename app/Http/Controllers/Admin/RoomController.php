<?php
// app/Http/Controllers/Admin/RoomController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * Afficher la liste des chambres
     */
    public function index(Request $request)
    {
        $query = Room::with('roomType');
        
        // Filtres
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('room_type') && $request->room_type != '') {
            $query->where('room_type_id', $request->room_type);
        }
        
        if ($request->has('floor') && $request->floor != '') {
            $query->where('floor', $request->floor);
        }
        
        $rooms = $query->paginate(15);
        $roomTypes = RoomType::all();
        $statuses = ['available', 'occupied', 'maintenance', 'out_of_service'];
        
        return view('admin.rooms.index', compact('rooms', 'roomTypes', 'statuses'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $roomTypes = RoomType::all();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    /**
     * Enregistrer une nouvelle chambre
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:0|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:10',
            'status' => 'required|in:available,occupied,maintenance,out_of_service',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $room = Room::create($request->all());

        // Upload des images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('rooms', 'public');
                
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.rooms.index')
                        ->with('success', 'Chambre créée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Room $room)
    {
        $roomTypes = RoomType::all();
        $room->load('images');
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    /**
     * Mettre à jour une chambre
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:0|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:10',
            'status' => 'required|in:available,occupied,maintenance,out_of_service',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $room->update($request->all());

        // Upload des nouvelles images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('rooms', 'public');
                
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.rooms.index')
                        ->with('success', 'Chambre mise à jour avec succès.');
    }

    /**
     * Supprimer une chambre
     */
    public function destroy(Room $room)
    {
        // Vérifier s'il y a des réservations futures
        $hasFutureReservations = $room->reservations()
                                      ->whereIn('status', ['confirmed', 'pending'])
                                      ->where('check_in_date', '>', now())
                                      ->exists();
        
        if ($hasFutureReservations) {
            return back()->with('error', 'Impossible de supprimer cette chambre car elle a des réservations futures.');
        }

        // Supprimer les images
        foreach ($room->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
                        ->with('success', 'Chambre supprimée avec succès.');
    }

    /**
     * Upload d'image supplémentaire
     */
    public function uploadImage(Request $request, Room $room)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = $request->file('image')->store('rooms', 'public');
        
        RoomImage::create([
            'room_id' => $room->id,
            'image_path' => $path
        ]);

        return response()->json(['success' => true, 'path' => $path]);
    }

    /**
     * Supprimer une image
     */
    public function deleteImage(RoomImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }
}
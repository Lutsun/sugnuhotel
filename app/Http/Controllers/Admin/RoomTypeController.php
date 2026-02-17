<?php
// app/Http/Controllers/Admin/RoomTypeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomTypeController extends Controller
{
    /**
     * Afficher la liste des types de chambres
     */
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->paginate(10);
        return view('admin.room-types.index', compact('roomTypes'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.room-types.create');
    }

    /**
     * Enregistrer un nouveau type de chambre
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('room-types', 'public');
            $data['image'] = $path;
        }

        RoomType::create($data);

        return redirect()->route('admin.room-types.index')
                        ->with('success', 'Type de chambre créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(RoomType $roomType)
    {
        return view('admin.room-types.edit', compact('roomType'));
    }

    /**
     * Mettre à jour un type de chambre
     */
    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name,' . $roomType->id,
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($roomType->image) {
                Storage::disk('public')->delete($roomType->image);
            }
            
            $path = $request->file('image')->store('room-types', 'public');
            $data['image'] = $path;
        }

        $roomType->update($data);

        return redirect()->route('admin.room-types.index')
                        ->with('success', 'Type de chambre mis à jour avec succès.');
    }

    /**
     * Supprimer un type de chambre
     */
    public function destroy(RoomType $roomType)
    {
        // Vérifier s'il y a des chambres de ce type
        if ($roomType->rooms()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce type car il y a des chambres associées.');
        }

        // Supprimer l'image
        if ($roomType->image) {
            Storage::disk('public')->delete($roomType->image);
        }

        $roomType->delete();

        return redirect()->route('admin.room-types.index')
                        ->with('success', 'Type de chambre supprimé avec succès.');
    }
}
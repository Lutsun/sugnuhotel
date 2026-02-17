<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        Service::create($request->all());

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service créé avec succès.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $service->update($request->all());

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service mis à jour avec succès.');
    }

    public function destroy(Service $service)
    {
        // Vérifier si le service est utilisé dans des réservations
        if ($service->reservations()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce service car il est utilisé dans des réservations.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service supprimé avec succès.');
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $service->is_active
        ]);
    }
}
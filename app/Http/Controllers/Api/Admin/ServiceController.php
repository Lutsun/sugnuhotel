<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return ServiceResource::collection(Service::paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        return new ServiceResource(Service::create($validated));
    }

    public function show(Service $service)
    {
        return new ServiceResource($service);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,'.$service->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return new ServiceResource($service);
    }

    public function destroy(Service $service)
    {
        if ($service->reservations()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce service car il est utilisé dans des réservations.',
            ], 422);
        }

        $service->delete();

        return response()->json(['message' => 'Service supprimé avec succès.']);
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);

        return new ServiceResource($service);
    }
}

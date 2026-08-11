<?php

namespace App\Http\Controllers\Api\Reception;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Mail\ReservationCancelled;
use App\Mail\ReservationModified;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('check_in_date', '<=', $request->date)
                  ->whereDate('check_out_date', '>=', $request->date);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reservation_number', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', function ($user) use ($request) {
                      $user->where('name', 'like', '%'.$request->search.'%')
                           ->orWhere('email', 'like', '%'.$request->search.'%');
                  });
            });
        }

        return ReservationResource::collection(
            $query->orderBy('check_in_date', 'desc')->paginate(15)
        );
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'room.roomType', 'room.images', 'services.service']);

        return new ReservationResource($reservation);
    }

    /**
     * Création d'une réservation par le personnel (au comptoir).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_adults' => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $reservation = DB::transaction(function () use ($validated, $request) {
            $room = Room::where('id', $validated['room_id'])->lockForUpdate()->firstOrFail();

            if (! Room::isAvailableBetween($room->id, $validated['check_in_date'], $validated['check_out_date'])) {
                abort(response()->json(['message' => 'Cette chambre n\'est pas disponible pour ces dates.'], 409));
            }

            $nights = Carbon::parse($validated['check_in_date'])->diffInDays($validated['check_out_date']);
            $totalPrice = $room->price_per_night * $nights;

            $services = $request->has('services')
                ? Service::whereIn('id', $request->services)->get()
                : collect();
            $totalPrice += $services->sum('price');

            $reservation = Reservation::create([
                'reservation_number' => $this->generateReservationNumber(),
                'user_id' => $validated['user_id'],
                'room_id' => $room->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'number_of_adults' => $validated['number_of_adults'],
                'number_of_children' => $validated['number_of_children'] ?? 0,
                'total_price' => $totalPrice,
                'status' => 'confirmed',
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            foreach ($services as $service) {
                $reservation->services()->create([
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'price' => $service->price,
                ]);
            }

            return $reservation;
        });

        return new ReservationResource($reservation->load(['user', 'room.roomType', 'services.service']));
    }

    /**
     * Modifier une réservation existante (dates, chambre, voyageurs, statut) — action qui manquait
     * totalement côté personnel. Prévient le client par email si les dates ou la chambre changent.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'room_id' => 'sometimes|exists:rooms,id',
            'check_in_date' => 'sometimes|date',
            'check_out_date' => 'sometimes|date|after:check_in_date',
            'number_of_adults' => 'sometimes|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
            'status' => 'sometimes|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $newRoomId = $validated['room_id'] ?? $reservation->room_id;
        $newCheckIn = $validated['check_in_date'] ?? $reservation->check_in_date->format('Y-m-d');
        $newCheckOut = $validated['check_out_date'] ?? $reservation->check_out_date->format('Y-m-d');
        $datesOrRoomChanged = $newRoomId != $reservation->room_id
            || $newCheckIn !== $reservation->check_in_date->format('Y-m-d')
            || $newCheckOut !== $reservation->check_out_date->format('Y-m-d');

        if ($datesOrRoomChanged && ! Room::isAvailableBetween($newRoomId, $newCheckIn, $newCheckOut, $reservation->id)) {
            return response()->json(['message' => 'Cette chambre n\'est pas disponible pour ces nouvelles dates.'], 409);
        }

        $previousCheckIn = $reservation->check_in_date->format('Y-m-d');
        $previousCheckOut = $reservation->check_out_date->format('Y-m-d');

        DB::transaction(function () use ($reservation, $validated, $newRoomId, $newCheckIn, $newCheckOut) {
            if (isset($validated['room_id']) && $validated['room_id'] != $reservation->room_id) {
                $nights = Carbon::parse($newCheckIn)->diffInDays($newCheckOut);
                $newRoom = Room::findOrFail($newRoomId);
                $servicesTotal = $reservation->services()->sum('price');
                $reservation->total_price = $newRoom->price_per_night * $nights + $servicesTotal;
            } elseif (isset($validated['check_in_date']) || isset($validated['check_out_date'])) {
                $nights = Carbon::parse($newCheckIn)->diffInDays($newCheckOut);
                $servicesTotal = $reservation->services()->sum('price');
                $reservation->total_price = $reservation->room->price_per_night * $nights + $servicesTotal;
            }

            $reservation->fill($validated)->save();
        });

        $reservation->load(['user', 'room.roomType', 'services.service']);

        if ($datesOrRoomChanged) {
            try {
                Mail::to($reservation->user->email)->send(
                    new ReservationModified($reservation, $previousCheckIn, $previousCheckOut)
                );
            } catch (\Exception $e) {
                Log::error('Erreur envoi email de modification: '.$e->getMessage());
            }
        }

        return new ReservationResource($reservation);
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return response()->json([
                'message' => 'Ce client ne peut pas être enregistré (statut: '.$reservation->status.')',
            ], 422);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'checked_in']);
            $reservation->room->update(['status' => 'occupied']);
        });

        return new ReservationResource($reservation->fresh(['user', 'room.roomType', 'services.service']));
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return response()->json([
                'message' => 'Ce client n\'est pas enregistré comme étant présent.',
            ], 422);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'checked_out']);
            $reservation->room->update(['status' => 'available']);
        });

        return new ReservationResource($reservation->fresh(['user', 'room.roomType', 'services.service']));
    }

    public function cancel(Reservation $reservation)
    {
        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Cette réservation ne peut pas être annulée.'], 422);
        }

        $wasConfirmed = $reservation->status === 'confirmed';

        DB::transaction(function () use ($reservation, $wasConfirmed) {
            $reservation->update(['status' => 'cancelled']);

            if ($wasConfirmed) {
                $reservation->room->update(['status' => 'available']);
            }
        });

        $reservation->load(['user', 'room.roomType', 'services.service']);

        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelled($reservation));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email d\'annulation: '.$e->getMessage());
        }

        return new ReservationResource($reservation);
    }

    public function search(Request $request)
    {
        $query = Reservation::with(['user', 'room']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($room) use ($search) {
                      $room->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        return ReservationResource::collection($query->limit(10)->get());
    }

    /**
     * Formulaire de création côté personnel : chambres disponibles, clients, services actifs.
     */
    public function createFormOptions()
    {
        return response()->json([
            'available_rooms' => Room::with('roomType')->where('status', 'available')->get()
                ->map(fn (Room $room) => [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->roomType->name,
                    'price_per_night' => (float) $room->price_per_night,
                    'max_occupancy' => $room->max_occupancy,
                ]),
            'clients' => User::where('role', 'client')->get(['id', 'name', 'email']),
            'services' => Service::where('is_active', true)->get(['id', 'name', 'price']),
        ]);
    }

    private function generateReservationNumber(): string
    {
        return 'RES'.date('Y').date('m').strtoupper(substr(uniqid(), -6));
    }
}

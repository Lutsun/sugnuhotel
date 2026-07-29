<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\ServiceResource;
use App\Mail\ReservationCancelled;
use App\Mail\ReservationConfirmation;
use App\Models\Reservation;
use App\Models\ReservationService;
use App\Models\Room;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    /**
     * Recherche de chambres disponibles (miroir stateless de BookingController::search : pas de session,
     * les dates/adultes/enfants sont renvoyés au client pour être réutilisés lors de la création).
     */
    public function search(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1|max:10',
            'children' => 'nullable|integer|min:0|max:10',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        $capacity = $request->adults + ($request->children ?? 0);

        $reservedRoomIds = Reservation::whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->pluck('room_id');

        $query = Room::with(['roomType', 'images'])
            ->whereNotIn('id', $reservedRoomIds)
            ->where('status', 'available')
            ->where('max_occupancy', '>=', $capacity);

        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->filled('room_types')) {
            $query->whereIn('room_type_id', (array) $request->room_types);
        }

        match ($request->input('sort')) {
            'price_desc' => $query->orderBy('price_per_night', 'desc'),
            'capacity_asc' => $query->orderBy('max_occupancy', 'asc'),
            'capacity_desc' => $query->orderBy('max_occupancy', 'desc'),
            default => $query->orderBy('price_per_night', 'asc'),
        };

        $availableRooms = $query->paginate(9)->withQueryString();

        return response()->json([
            'rooms' => RoomResource::collection($availableRooms->items()),
            'meta' => [
                'check_in' => $checkIn->format('Y-m-d'),
                'check_out' => $checkOut->format('Y-m-d'),
                'nights' => $nights,
                'adults' => (int) $request->adults,
                'children' => (int) ($request->children ?? 0),
                'current_page' => $availableRooms->currentPage(),
                'last_page' => $availableRooms->lastPage(),
                'per_page' => $availableRooms->perPage(),
                'total' => $availableRooms->total(),
            ],
        ]);
    }

    /**
     * Vérification rapide de disponibilité (AJAX), miroir de checkAvailability.
     */
    public function checkAvailability(Request $request, Room $room)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $available = Room::isAvailableBetween($room->id, $request->check_in, $request->check_out);

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Chambre disponible' : 'Chambre non disponible pour ces dates',
        ]);
    }

    /**
     * Détails nécessaires à l'écran de confirmation (remplace la dépendance à session('search')).
     */
    public function confirm(Request $request, Room $room)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        $room->load('roomType');
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        return response()->json([
            'room' => new RoomResource($room),
            'services' => ServiceResource::collection(Service::where('is_active', true)->get()),
            'nights' => $nights,
            'room_price' => $room->price_per_night * $nights,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'adults' => (int) $request->adults,
            'children' => (int) ($request->children ?? 0),
        ]);
    }

    /**
     * Créer la réservation. Verrouille la chambre (lockForUpdate) le temps de la transaction
     * pour réduire la fenêtre de race condition du double-booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:500',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $reservation = DB::transaction(function () use ($validated, $request) {
            $room = Room::where('id', $validated['room_id'])->lockForUpdate()->firstOrFail();

            if (! Room::isAvailableBetween($room->id, $validated['check_in'], $validated['check_out'])) {
                abort(response()->json([
                    'message' => 'Désolé, cette chambre n\'est plus disponible pour ces dates.',
                ], 409));
            }

            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $nights = $checkIn->diffInDays($checkOut);

            $totalPrice = $room->price_per_night * $nights;

            $services = $request->has('services')
                ? Service::whereIn('id', $request->services)->get()
                : collect();

            $totalPrice += $services->sum('price');

            $reservation = Reservation::create([
                'reservation_number' => $this->generateReservationNumber(),
                'user_id' => Auth::id(),
                'room_id' => $room->id,
                'check_in_date' => $validated['check_in'],
                'check_out_date' => $validated['check_out'],
                'number_of_adults' => $validated['adults'],
                'number_of_children' => $validated['children'] ?? 0,
                'total_price' => $totalPrice,
                'status' => 'confirmed',
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            foreach ($services as $service) {
                ReservationService::create([
                    'reservation_id' => $reservation->id,
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'price' => $service->price,
                ]);
            }

            $room->update(['status' => 'occupied']);

            return $reservation;
        });

        $reservation->load(['room.roomType', 'room.images', 'services.service', 'user']);

        try {
            Mail::to($reservation->user->email)->send(new ReservationConfirmation($reservation));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email de confirmation: '.$e->getMessage());
        }

        return new ReservationResource($reservation);
    }

    public function myReservations()
    {
        $reservations = Reservation::with(['room.roomType', 'services.service'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return ReservationResource::collection($reservations);
    }

    public function show($id)
    {
        $reservation = Reservation::with(['room.roomType', 'room.images', 'services.service'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return new ReservationResource($reservation);
    }

    /**
     * Annuler une réservation (règle des 24h avant l'arrivée) et prévenir le client par email.
     */
    public function cancel($id)
    {
        $reservation = Reservation::with(['room', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $checkIn = Carbon::parse($reservation->check_in_date);
        $hoursUntilCheckIn = Carbon::now()->diffInHours($checkIn, false);

        if ($hoursUntilCheckIn < 0) {
            return response()->json(['message' => 'Impossible d\'annuler une réservation déjà passée.'], 422);
        }

        if ($hoursUntilCheckIn < 24) {
            return response()->json([
                'message' => "Impossible d'annuler une réservation moins de 24h avant l'arrivée. (".floor($hoursUntilCheckIn).'h restantes)',
            ], 422);
        }

        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json([
                'message' => 'Cette réservation ne peut pas être annulée car son statut est "'.$reservation->status.'".',
            ], 422);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);
            $reservation->room->update(['status' => 'available']);
        });

        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelled($reservation));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email d\'annulation: '.$e->getMessage());
        }

        return new ReservationResource($reservation->fresh(['room.roomType', 'services.service', 'user']));
    }

    private function generateReservationNumber(): string
    {
        return 'RES'.date('Y').date('m').strtoupper(substr(uniqid(), -6));
    }
}

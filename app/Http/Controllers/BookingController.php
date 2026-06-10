<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReservationConfirmation;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Rechercher les chambres disponibles
     */
    public function search(Request $request)
{
    $request->validate([
        'check_in' => 'required|date|after_or_equal:today',
        'check_out' => 'required|date|after:check_in',
        'adults' => 'required|integer|min:1|max:10',
        'children' => 'nullable|integer|min:0|max:10'
    ]);

    $checkIn = Carbon::parse($request->check_in);
    $checkOut = Carbon::parse($request->check_out);
    $nights = $checkIn->diffInDays($checkOut);
    $capacity = $request->adults + ($request->children ?? 0);
    
    // Récupérer les IDs des chambres réservées
    $reservedRoomIds = Reservation::whereIn('status', ['confirmed', 'checked_in'])
        ->where(function ($query) use ($checkIn, $checkOut) {
            $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function ($q) use ($checkIn, $checkOut) {
                      $q->where('check_in_date', '<=', $checkIn)
                        ->where('check_out_date', '>=', $checkOut);
                  });
        })
        ->pluck('room_id')
        ->toArray();
    
    // Construire la requête
    $query = Room::with(['roomType', 'images'])
        ->whereNotIn('id', $reservedRoomIds)
        ->where('status', 'available')
        ->where('max_occupancy', '>=', $capacity);
    
    // Appliquer les filtres de prix
    if ($request->filled('min_price')) {
        $query->where('price_per_night', '>=', $request->min_price);
    }
    
    if ($request->filled('max_price')) {
        $query->where('price_per_night', '<=', $request->max_price);
    }
    
    // Appliquer les filtres de type de chambre
    if ($request->filled('room_types')) {
        $query->whereIn('room_type_id', $request->room_types);
    }
    
    // Appliquer les filtres d'équipements
    if ($request->has('wifi')) {
        $query->where('has_wifi', true);
    }
    if ($request->has('tv')) {
        $query->where('has_tv', true);
    }
    if ($request->has('air_conditioning')) {
        $query->where('has_air_conditioning', true);
    }
    if ($request->has('minibar')) {
        $query->where('has_minibar', true);
    }
    
    // Appliquer le tri
    switch ($request->sort) {
        case 'price_desc':
            $query->orderBy('price_per_night', 'desc');
            break;
        case 'capacity_asc':
            $query->orderBy('max_occupancy', 'asc');
            break;
        case 'capacity_desc':
            $query->orderBy('max_occupancy', 'desc');
            break;
        default: // price_asc
            $query->orderBy('price_per_night', 'asc');
    }
    
    // Exécuter avec pagination
    $availableRooms = $query->paginate(9)->withQueryString();
    
    // Sauvegarder la recherche en session
    session([
        'search' => [
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'nights' => $nights
        ]
    ]);
    
    return view('booking.search', compact('availableRooms', 'checkIn', 'checkOut', 'nights'));
}

    /**
     * Vérifier la disponibilité en temps réel
     */
    public function checkAvailability(Request $request)
    {
        try {
            $roomId = $request->room_id;
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            $isAvailable = $this->isRoomAvailable($roomId, $checkIn, $checkOut);
            
            return response()->json([
                'available' => $isAvailable,
                'message' => $isAvailable ? 'Chambre disponible' : 'Chambre non disponible pour ces dates'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => 'Erreur lors de la vérification'
            ], 500);
        }
    }

   /**
 * Page de confirmation avant réservation
 */
public function confirm($roomId)
{
    $room = Room::with('roomType')->findOrFail($roomId);
    $search = session('search');
    
    if (!$search) {
        return redirect()->route('home')->with('error', 'Veuillez d\'abord effectuer une recherche');
    }
    
    $services = Service::where('is_active', true)->get();
    $nights = $search['nights'];
    $roomPrice = $room->price_per_night * $nights;
    
    // Extraire les données de la session
    $checkIn = $search['check_in'];
    $checkOut = $search['check_out'];
    $adults = $search['adults'];
    $children = $search['children'];
    
    return view('booking.confirm', compact(
        'room', 
        'services', 
        'nights', 
        'roomPrice',
        'checkIn',
        'checkOut',
        'adults',
        'children'
    ));
}

    /**
     * Enregistrer la réservation
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:500',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id'
        ]);

        // Vérifier la disponibilité (protection contre double booking)
        if (!$this->isRoomAvailable($request->room_id, $request->check_in, $request->check_out)) {
            return back()->with('error', 'Désolé, cette chambre n\'est plus disponible pour ces dates.');
        }

        DB::beginTransaction();
        
        try {
            // Calculer le prix total
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            $nights = $checkIn->diffInDays($checkOut);
            
            $room = Room::find($request->room_id);
            $totalPrice = $room->price_per_night * $nights;
            
            // Ajouter les services
            $servicesTotal = 0;
            if ($request->has('services')) {
                $services = Service::whereIn('id', $request->services)->get();
                foreach ($services as $service) {
                    $servicesTotal += $service->price;
                }
            }
            
            $totalPrice += $servicesTotal;
            
            // Créer la réservation
            $reservation = Reservation::create([
                'reservation_number' => $this->generateReservationNumber(),
                'user_id' => Auth::id(),
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in,
                'check_out_date' => $request->check_out,
                'number_of_adults' => $request->adults,
                'number_of_children' => $request->children ?? 0,
                'total_price' => $totalPrice,
                'status' => 'confirmed',
                'special_requests' => $request->special_requests
            ]);
            
            // Ajouter les services à la réservation
            if ($request->has('services')) {
                foreach ($request->services as $serviceId) {
                    $service = Service::find($serviceId);
                    ReservationService::create([
                        'reservation_id' => $reservation->id,
                        'service_id' => $serviceId,
                        'quantity' => 1,
                        'price' => $service->price
                    ]);
                }
            }
            
            // Mettre à jour le statut de la chambre
            $room->update(['status' => 'occupied']);
            
            DB::commit();
            
            // Envoyer l'email de confirmation
            try {
                $user = Auth::user();
                if ($user) {
                    Mail::to($user->email)->send(new ReservationConfirmation($reservation));
                }
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas bloquer la réservation
                Log::error('Erreur envoi email: ' . $e->getMessage());
            }
            
            // Nettoyer la session
            session()->forget('search');
            
            return redirect()->route('reservation.show', $reservation->id)
                           ->with('success', 'Réservation effectuée avec succès ! Votre numéro de réservation est : ' . $reservation->reservation_number);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la réservation. Veuillez réessayer.');
        }
    }

    /**
     * Afficher les réservations du client
     */
    public function myReservations()
    {
        $reservations = Reservation::with(['room', 'services'])
                                   ->where('user_id', Auth::id())
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);
        
        return view('booking.my-reservations', compact('reservations'));
    }

    /**
     * Afficher le détail d'une réservation
     */
    public function show($id)
    {
        $reservation = Reservation::with(['room.roomType', 'room.images', 'services.service'])
                                  ->where('user_id', Auth::id())
                                  ->findOrFail($id);
        
        return view('booking.show', compact('reservation'));
    }

    /**
     * Annuler une réservation
     */
    /** */
   
    public function cancel($id)
    {
        try {
            // Récupérer la réservation
            $reservation = Reservation::where('user_id', Auth::id())->findOrFail($id);
            
            // Log pour debug
            Log::info('Tentative annulation', [
                'reservation_id' => $id,
                'status_actuel' => $reservation->status,
                'user_id' => Auth::id()
            ]);
            
            // Vérifier si l'annulation est possible
            $checkIn = Carbon::parse($reservation->check_in_date);
            $now = Carbon::now();
            
            // Calculer la différence en heures (valeur absolue)
            $hoursUntilCheckIn = $now->diffInHours($checkIn, false);
            
            // Si la date est passée (différence négative)
            if ($hoursUntilCheckIn < 0) {
                return back()->with('error', 'Impossible d\'annuler une réservation déjà passée.');
            }
            
            // Si moins de 24h avant l'arrivée
            if ($hoursUntilCheckIn < 24) {
                $heuresRestantes = floor($hoursUntilCheckIn);
                return back()->with('error', "Impossible d'annuler une réservation moins de 24h avant l'arrivée. ({$heuresRestantes}h restantes)");
            }
            
            // Vérifier le statut
            if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                return back()->with('error', 'Cette réservation ne peut pas être annulée car son statut est "' . $reservation->status . '".');
            }
            
            DB::beginTransaction();
            
            // Mettre à jour le statut de la réservation
            $reservation->update([
                'status' => 'cancelled',
                'updated_at' => now()
            ]);
            
            // Vérifier que la mise à jour a bien fonctionné
            $reservation->refresh();
            
            // Log pour vérifier la mise à jour
            Log::info('Réservation annulée avec succès', [
                'reservation_id' => $id,
                'nouveau_status' => $reservation->status
            ]);
            
            // Libérer la chambre
            $reservation->room->update(['status' => 'available']);
            
            DB::commit();
            
            return redirect()->route('booking.my-reservations')
                            ->with('success', 'Réservation #' . $reservation->reservation_number . ' annulée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur annulation: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier la disponibilité d'une chambre
     */
    private function isRoomAvailable($roomId, $checkIn, $checkOut)
    {
        $existingReservation = Reservation::where('room_id', $roomId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->exists();
        
        return !$existingReservation;
    }

    /**
     * Récupérer les chambres disponibles
     */

    private function getAvailableRooms($checkIn, $checkOut, $capacity)
    {
        $reservedRoomIds = Reservation::whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->pluck('room_id')
            ->toArray();
        
        return Room::with('roomType')
            ->whereNotIn('id', $reservedRoomIds)
            ->where('status', 'available')
            ->where('max_occupancy', '>=', $capacity)
            ->get();
    }

    /**
     * Générer un numéro de réservation unique
     */
    private function generateReservationNumber()
    {
        $prefix = 'RES';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . $year . $month . $random;
    }
}
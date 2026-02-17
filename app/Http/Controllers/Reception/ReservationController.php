<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'room']);
        
        // Filtres
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date')) {
            $query->whereDate('check_in_date', '<=', $request->date)
                  ->whereDate('check_out_date', '>=', $request->date);
        }
        
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('reservation_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($user) use ($request) {
                      $user->where('name', 'like', '%' . $request->search . '%')
                           ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $reservations = $query->orderBy('check_in_date', 'desc')->paginate(15);
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
        
        return view('reception.reservations.index', compact('reservations', 'statuses'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'room.roomType', 'services.service']);
        return view('reception.reservations.show', compact('reservation'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        $users = User::where('role', 'client')->get();
        $services = Service::where('is_active', true)->get();
        
        return view('reception.reservations.create', compact('rooms', 'users', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_adults' => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id'
        ]);

        // Vérifier la disponibilité
        $isAvailable = $this->checkRoomAvailability(
            $request->room_id, 
            $request->check_in_date, 
            $request->check_out_date
        );
        
        if (!$isAvailable) {
            return back()->with('error', 'Cette chambre n\'est pas disponible pour ces dates.')
                        ->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Calculer le prix
            $checkIn = Carbon::parse($request->check_in_date);
            $checkOut = Carbon::parse($request->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            
            $room = Room::find($request->room_id);
            $totalPrice = $room->price_per_night * $nights;
            
            // Ajouter les services
            if ($request->has('services')) {
                $services = Service::whereIn('id', $request->services)->get();
                foreach ($services as $service) {
                    $totalPrice += $service->price;
                }
            }
            
            // Créer la réservation
            $reservation = Reservation::create([
                'reservation_number' => $this->generateReservationNumber(),
                'user_id' => $request->user_id,
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'number_of_adults' => $request->number_of_adults,
                'number_of_children' => $request->number_of_children ?? 0,
                'total_price' => $totalPrice,
                'status' => 'confirmed',
                'special_requests' => $request->special_requests
            ]);
            
            // Ajouter les services
            if ($request->has('services')) {
                foreach ($request->services as $serviceId) {
                    $service = Service::find($serviceId);
                    $reservation->services()->create([
                        'service_id' => $serviceId,
                        'quantity' => 1,
                        'price' => $service->price
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('reception.reservations.show', $reservation)
                            ->with('success', 'Réservation créée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la réservation.')
                        ->withInput();
        }
    }

    public function checkIn(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['confirmed'])) {
            return back()->with('error', 'Ce client ne peut pas être enregistré (statut: ' . $reservation->status . ')');
        }
        
        DB::beginTransaction();
        
        try {
            $reservation->update(['status' => 'checked_in']);
            $reservation->room->update(['status' => 'occupied']);
            
            DB::commit();
            
            return back()->with('success', 'Check-in effectué avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du check-in.');
        }
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Ce client n\'est pas enregistré comme étant présent.');
        }
        
        DB::beginTransaction();
        
        try {
            $reservation->update(['status' => 'checked_out']);
            $reservation->room->update(['status' => 'available']);
            
            DB::commit();
            
            return back()->with('success', 'Check-out effectué avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du check-out.');
        }
    }

    public function cancel(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }
        
        DB::beginTransaction();
        
        try {
            $reservation->update(['status' => 'cancelled']);
            
            // Libérer la chambre si elle était confirmée
            if ($reservation->status === 'confirmed') {
                $reservation->room->update(['status' => 'available']);
            }
            
            DB::commit();
            
            return back()->with('success', 'Réservation annulée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'annulation.');
        }
    }

    public function search(Request $request)
    {
        $query = Reservation::with(['user', 'room']);
        
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function($room) use ($search) {
                      $room->where('room_number', 'like', "%{$search}%");
                  });
            });
        }
        
        $reservations = $query->limit(10)->get();
        
        if ($request->ajax()) {
            return response()->json($reservations);
        }
        
        return view('reception.reservations.search-results', compact('reservations'));
    }

    private function checkRoomAvailability($roomId, $checkIn, $checkOut)
    {
        return !Reservation::where('room_id', $roomId)
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
    }

    private function generateReservationNumber()
    {
        $prefix = 'RES';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . $year . $month . $random;
    }
}
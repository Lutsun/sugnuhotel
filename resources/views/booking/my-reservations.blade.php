{{-- resources/views/booking/my-reservations.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes réservations - SugnuHotel')

@section('content')
{{-- En-tête --}}
<section class="page-header" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; padding: 150px 0 80px;">
    <div class="container">
        <h1 class="page-title" style="color: var(--light); font-size: 48px; margin-bottom: 20px;">Mes réservations</h1>
        <div class="breadcrumbs" style="color: rgba(255,255,255,0.8);">
            <a href="{{ route('home') }}" style="color: var(--light);">Accueil</a> / 
            <a href="{{ route('profile.show') }}" style="color: var(--light);">Profil</a> / 
            <span>Réservations</span>
        </div>
    </div>
</section>

{{-- Filtres --}}
<section style="padding: 40px 0 0;">
    <div class="container">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
            <a href="{{ route('booking.my-reservations', ['status' => 'all']) }}" 
               class="filter-btn {{ request('status', 'all') == 'all' ? 'active' : '' }}"
               style="padding: 10px 25px; border-radius: 30px; text-decoration: none; transition: var(--transition);">
                Toutes
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'pending']) }}" 
               class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}"
               style="padding: 10px 25px; border-radius: 30px; text-decoration: none; transition: var(--transition);">
                En attente
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'confirmed']) }}" 
               class="filter-btn {{ request('status') == 'confirmed' ? 'active' : '' }}"
               style="padding: 10px 25px; border-radius: 30px; text-decoration: none; transition: var(--transition);">
                Confirmées
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'checked_in']) }}" 
               class="filter-btn {{ request('status') == 'checked_in' ? 'active' : '' }}"
               style="padding: 10px 25px; border-radius: 30px; text-decoration: none; transition: var(--transition);">
                En cours
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'completed']) }}" 
               class="filter-btn {{ request('status') == 'completed' ? 'active' : '' }}"
               style="padding: 10px 25px; border-radius: 30px; text-decoration: none; transition: var(--transition);">
                Terminées
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'cancelled']) }}" 
               class="filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}"
               style="padding: 10px 25px; border-radius: 30px; text-decoration: none; transition: var(--transition);">
                Annulées
            </a>
        </div>
    </div>
</section>

{{-- Liste des réservations --}}
<section style="padding: 40px 0 60px;">
    <div class="container">
        @if($reservations->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($reservations as $reservation)
                    <div class="reservation-card" style="background: var(--light); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: var(--transition);">
                        <div style="display: grid; grid-template-columns: 200px 1fr;">
                            {{-- Image --}}
                            <div style="height: 200px; overflow: hidden;">
                                <img src="{{ $reservation->room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                     alt="Chambre {{ $reservation->room->room_number }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            
                            {{-- Détails --}}
                            <div style="padding: 25px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <div>
                                        <h3 style="font-size: 22px; margin-bottom: 5px;">
                                            Réservation #{{ $reservation->id }}
                                        </h3>
                                        <p style="color: var(--text-light);">
                                            Chambre {{ $reservation->room->room_number }} - {{ $reservation->room->roomType->name }}
                                        </p>
                                    </div>
                                    <div>
                                        @php
                                            $statusClasses = [
                                                'pending' => ['bg' => '#ffc107', 'text' => 'En attente'],
                                                'confirmed' => ['bg' => '#28a745', 'text' => 'Confirmée'],
                                                'checked_in' => ['bg' => '#17a2b8', 'text' => 'En cours'],
                                                'checked_out' => ['bg' => '#6c757d', 'text' => 'Terminée'],
                                                'cancelled' => ['bg' => '#dc3545', 'text' => 'Annulée'],
                                                'completed' => ['bg' => '#28a745', 'text' => 'Terminée'],
                                            ];
                                            $status = $statusClasses[$reservation->status] ?? ['bg' => '#6c757d', 'text' => $reservation->status];
                                        @endphp
                                        <span style="background: '{{ $status['bg'] }}'; color: var(--light); padding: 5px 15px; border-radius: 30px; font-size: 12px;" >
                                            {{ $status['text'] }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
                                    <div>
                                        <small style="color: var(--text-light);">Arrivée</small>
                                        <p style="font-weight: 500;">{{ date('d/m/Y', strtotime($reservation->check_in)) }}</p>
                                    </div>
                                    <div>
                                        <small style="color: var(--text-light);">Départ</small>
                                        <p style="font-weight: 500;">{{ date('d/m/Y', strtotime($reservation->check_out)) }}</p>
                                    </div>
                                    <div>
                                        <small style="color: var(--text-light);">Total</small>
                                        <p style="font-weight: 700; color: var(--primary);">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>
                                
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <a href="{{ route('reservation.show', $reservation->id) }}" class="btn-secondary" style="padding: 10px 20px;">
                                        <i class="fas fa-eye"></i> Détails
                                    </a>
                                    
                                    @if(in_array($reservation->status, ['pending', 'confirmed']))
                                        <button onclick="showCancelModal('{{ $reservation->id }}')" class="btn-secondary" style="padding: 10px 20px; background: #dc3545; color: var(--light); border-color: #dc3545;">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    @endif
                                    
                                    @if($reservation->status == 'completed' && !$reservation->review)
                                        <a href="{{ route('review.create', $reservation->id) }}" class="btn-primary" style="padding: 10px 20px;">
                                            <i class="fas fa-star"></i> Donner un avis
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div style="margin-top: 50px;">
                {{ $reservations->appends(request()->query())->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 80px 40px; background: var(--light); border-radius: 15px;">
                <i class="fas fa-calendar-times" style="font-size: 60px; color: var(--primary); opacity: 0.5; margin-bottom: 20px;"></i>
                <h3 style="font-size: 24px; margin-bottom: 10px;">Aucune réservation</h3>
                <p style="color: var(--text-light); margin-bottom: 30px;">Vous n'avez pas encore de réservation dans cette catégorie.</p>
                <a href="{{ route('home') }}#booking" class="btn-primary">Réserver maintenant</a>
            </div>
        @endif
    </div>
</section>

{{-- Modal d'annulation --}}
<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--light); border-radius: 15px; padding: 40px; max-width: 400px; width: 90%; position: relative;">
        <h3 style="font-size: 24px; margin-bottom: 15px;">Confirmer l'annulation</h3>
        <p style="color: var(--text-light); margin-bottom: 25px;">Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.</p>
        
        <form id="cancelForm" method="POST">
            @csrf
            @method('DELETE')
            
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="hideCancelModal()" class="btn-secondary" style="flex: 1;">Fermer</button>
                <button type="submit" class="btn-primary" style="flex: 1; background: #dc3545;">Confirmer</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showCancelModal(reservationId) {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = `/reservation/${reservationId}/cancel`; // Adaptez selon votre route
        modal.style.display = 'flex';
    }
    
    function hideCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
    
    // Fermer le modal en cliquant dehors
    window.onclick = function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endpush

@push('styles')
<style>
    .filter-btn {
        background: var(--gray);
        color: var(--text);
        border: 1px solid transparent;
    }
    
    .filter-btn:hover {
        background: var(--primary);
        color: var(--light);
        transform: translateY(-2px);
    }
    
    .filter-btn.active {
        background: var(--primary);
        color: var(--light);
        border-color: var(--primary);
    }
    
    .reservation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    @media (max-width: 768px) {
        .reservation-card > div {
            grid-template-columns: 1fr !important;
        }
        
        .reservation-card > div > div:first-child {
            height: 150px;
        }
        
        .filter-btn {
            font-size: 13px;
            padding: 8px 15px !important;
        }
    }
</style>
@endpush
@endsection
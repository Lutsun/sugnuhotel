{{-- resources/views/booking/my-reservations.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes réservations - SugnuHotel')

@section('content')
{{-- En-tête --}}
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Mes réservations</h1>
        <div class="breadcrumbs">
            <a href="{{ route('home') }}">Accueil</a> / 
            <a href="{{ route('profile.show') }}">Profil</a> / 
            <span>Réservations</span>
        </div>
    </div>
</section>

{{-- Filtres --}}
<section class="filters-section">
    <div class="container">
        <div class="filters-wrapper">
            <a href="{{ route('booking.my-reservations', ['status' => 'all']) }}" 
               class="filter-btn {{ request('status', 'all') == 'all' ? 'active' : '' }}">
                Toutes
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'pending']) }}" 
               class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                En attente
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'confirmed']) }}" 
               class="filter-btn {{ request('status') == 'confirmed' ? 'active' : '' }}">
                Confirmées
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'checked_in']) }}" 
               class="filter-btn {{ request('status') == 'checked_in' ? 'active' : '' }}">
                En cours
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'completed']) }}" 
               class="filter-btn {{ request('status') == 'completed' ? 'active' : '' }}">
                Terminées
            </a>
            <a href="{{ route('booking.my-reservations', ['status' => 'cancelled']) }}" 
               class="filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}">
                Annulées
            </a>
        </div>
    </div>
</section>

{{-- Liste des réservations --}}
<section class="reservations-list-section">
    <div class="container">
        @if($reservations->count() > 0)
            <div class="reservations-grid">
                @foreach($reservations as $reservation)
                    <div class="reservation-card">
                        <div class="reservation-card-inner">
                            {{-- Image --}}
                            <div class="reservation-image">
                                <img src="{{ $reservation->room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                     alt="Chambre {{ $reservation->room->room_number }}">
                            </div>
                            
                            {{-- Détails --}}
                            <div class="reservation-details">
                                <div class="reservation-header">
                                    <div>
                                        <h3 class="reservation-title">
                                            Réservation #{{ $reservation->id }}
                                        </h3>
                                        <p class="reservation-room">
                                            Chambre {{ $reservation->room->room_number }} - {{ $reservation->room->roomType->name }}
                                        </p>
                                    </div>
                                    <div>
                                        @php
                                            $statusClasses = [
                                                'pending' => ['bg' => 'status-pending', 'text' => 'En attente'],
                                                'confirmed' => ['bg' => 'status-confirmed', 'text' => 'Confirmée'],
                                                'checked_in' => ['bg' => 'status-checked_in', 'text' => 'En cours'],
                                                'checked_out' => ['bg' => 'status-checked_out', 'text' => 'Terminée'],
                                                'cancelled' => ['bg' => 'status-cancelled', 'text' => 'Annulée'],
                                                'completed' => ['bg' => 'status-completed', 'text' => 'Terminée'],
                                            ];
                                            $status = $statusClasses[$reservation->status] ?? ['bg' => 'status-default', 'text' => $reservation->status];
                                        @endphp
                                        <span class="status-badge {{ $status['bg'] }}">
                                            {{ $status['text'] }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="reservation-dates">
                                    <div class="date-item">
                                        <small>Arrivée</small>
                                        <p class="date-value">{{ date('d/m/Y', strtotime($reservation->check_in_date)) }}</p>
                                    </div>
                                    <div class="date-item">
                                        <small>Départ</small>
                                        <p class="date-value">{{ date('d/m/Y', strtotime($reservation->check_out_date)) }}</p>
                                    </div>
                                    <div class="date-item">
                                        <small>Total</small>
                                        <p class="price-value">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>
                                
                                <div class="reservation-actions">
                                    <a href="{{ route('reservation.show', $reservation->id) }}" class="btn-details">
                                        <i class="fas fa-eye"></i> Détails
                                    </a>
                                    
                                    @if(in_array($reservation->status, ['pending', 'confirmed']))
                                        <button onclick="showCancelModal('{{ $reservation->id }}')" class="btn-cancel">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    @endif
                                    
                                    @if($reservation->status == 'completed' && !$reservation->review)
                                        <a href="{{ route('review.create', $reservation->id) }}" class="btn-review">
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
            <div class="pagination-wrapper">
                {{ $reservations->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-reservations">
                <i class="fas fa-calendar-times no-reservations-icon"></i>
                <h3>Aucune réservation</h3>
                <p>Vous n'avez pas encore de réservation dans cette catégorie.</p>
                <a href="{{ route('home') }}#booking" class="btn-primary">Réserver maintenant</a>
            </div>
        @endif
    </div>
</section>

{{-- Modal d'annulation --}}
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <h3>Confirmer l'annulation</h3>
        <p>Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.</p>
        
        <form id="cancelForm" method="POST">
            @csrf
            <div class="modal-actions">
                <button type="button" onclick="hideCancelModal()" class="btn-secondary">Fermer</button>
                <button type="submit" class="btn-confirm-cancel">Confirmer l'annulation</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showCancelModal(reservationId) {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = `/reservation/${reservationId}/cancel`;
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
    /* Page Header */
    .page-header {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        padding: 150px 0 80px;
    }

    .page-title {
        color: var(--light);
        font-size: 48px;
        margin-bottom: 20px;
    }

    .breadcrumbs {
        color: rgba(255,255,255,0.8);
    }

    .breadcrumbs a {
        color: var(--light);
        text-decoration: none;
    }

    .breadcrumbs a:hover {
        color: var(--primary);
    }

    /* Filtres */
    .filters-section {
        padding: 40px 0 0;
    }

    .filters-wrapper {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .filter-btn {
        padding: 10px 25px;
        border-radius: 30px;
        text-decoration: none;
        transition: var(--transition);
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

    /* Liste des réservations */
    .reservations-list-section {
        padding: 40px 0 60px;
    }

    .reservations-grid {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .reservation-card {
        background: var(--light);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: var(--transition);
    }

    .reservation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .reservation-card-inner {
        display: grid;
        grid-template-columns: 200px 1fr;
    }

    .reservation-image {
        height: 200px;
        overflow: hidden;
    }

    .reservation-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .reservation-details {
        padding: 25px;
    }

    .reservation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .reservation-title {
        font-size: 22px;
        margin-bottom: 5px;
    }

    .reservation-room {
        color: var(--text-light);
    }

    /* Status Badges */
    .status-badge {
        color: var(--light);
        padding: 5px 15px;
        border-radius: 30px;
        font-size: 12px;
        display: inline-block;
    }

    .status-pending {
        background: #ffc107;
    }

    .status-confirmed {
        background: #28a745;
    }

    .status-checked_in {
        background: #17a2b8;
    }

    .status-checked_out,
    .status-completed {
        background: #6c757d;
    }

    .status-cancelled {
        background: #dc3545;
    }

    .status-default {
        background: #6c757d;
    }

    /* Dates */
    .reservation-dates {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .date-item small {
        color: var(--text-light);
        display: block;
        margin-bottom: 5px;
    }

    .date-value {
        font-weight: 500;
    }

    .price-value {
        font-weight: 700;
        color: var(--primary);
    }

    /* Actions */
    .reservation-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-details {
        padding: 10px 20px;
        background: var(--primary);
        color: var(--light);
        text-decoration: none;
        border-radius: 30px;
        font-size: 13px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-details:hover {
        background: var(--primary-dark);
    }

    .btn-cancel {
        padding: 10px 20px;
        background: #dc3545;
        color: var(--light);
        border: none;
        border-radius: 30px;
        font-size: 13px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-cancel:hover {
        background: #c82333;
    }

    .btn-review {
        padding: 10px 20px;
        background: var(--primary);
        color: var(--light);
        text-decoration: none;
        border-radius: 30px;
        font-size: 13px;
        transition: var(--transition);
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 50px;
    }

    /* Empty state */
    .no-reservations {
        text-align: center;
        padding: 80px 40px;
        background: var(--light);
        border-radius: 15px;
    }

    .no-reservations-icon {
        font-size: 60px;
        color: var(--primary);
        opacity: 0.5;
        margin-bottom: 20px;
    }

    .no-reservations h3 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .no-reservations p {
        color: var(--text-light);
        margin-bottom: 30px;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: var(--light);
        border-radius: 15px;
        padding: 40px;
        max-width: 400px;
        width: 90%;
        position: relative;
    }

    .modal-content h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    .modal-content p {
        color: var(--text-light);
        margin-bottom: 25px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
    }

    .btn-confirm-cancel {
        flex: 1;
        padding: 12px;
        background: #dc3545;
        color: var(--light);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-confirm-cancel:hover {
        background: #c82333;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .reservation-card-inner {
            grid-template-columns: 1fr;
        }
        
        .reservation-image {
            height: 150px;
        }
        
        .filter-btn {
            font-size: 13px;
            padding: 8px 15px;
        }
        
        .reservation-dates {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        
        .reservation-actions {
            flex-wrap: wrap;
        }
        
        .btn-details, .btn-cancel, .btn-review {
            flex: 1;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 32px;
        }
        
        .reservation-header {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
@endpush
@endsection
{{-- resources/views/booking/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détail de la réservation - SugnuHotel')

@section('content')
{{-- En-tête --}}
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Détail de la réservation</h1>
        <div class="breadcrumbs">
            <a href="{{ route('home') }}">Accueil</a> / 
            <a href="{{ route('booking.my-reservations') }}">Mes réservations</a> / 
            <span>Détail</span>
        </div>
    </div>
</section>

{{-- Message de succès (si présent) --}}
@if(session('success'))
    <section class="success-message-section">
        <div class="container">
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>Merci pour votre réservation !</h2>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    </section>
@endif

{{-- Détails de la réservation --}}
<section class="reservation-detail-section">
    <div class="container">
        <div class="reservation-detail-card">
            {{-- En-tête avec numéro de réservation --}}
            <div class="reservation-detail-header">
                <div>
                    <h2 class="reservation-number">Réservation #{{ $reservation->reservation_number }}</h2>
                    <p class="reservation-date">Effectuée le {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
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
                    <span class="status-badge-lg {{ $status['bg'] }}">
                        {{ $status['text'] }}
                    </span>
                </div>
            </div>
            
            {{-- Grille d'informations --}}
            <div class="reservation-info-grid">
                {{-- Informations chambre --}}
                <div class="info-card">
                    <h4 class="info-title">
                        <i class="fas fa-bed"></i> Chambre réservée
                    </h4>
                    <div class="info-content">
                        <div class="room-info">
                            <div class="room-image-small">
                                <img src="{{ $reservation->room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                     alt="Chambre {{ $reservation->room->room_number }}">
                            </div>
                            <div>
                                <h5 class="room-number">Chambre {{ $reservation->room->room_number }}</h5>
                                <p class="room-type">{{ $reservation->room->roomType->name }}</p>
                                <p class="room-capacity">Capacité: {{ $reservation->room->max_occupancy }} personnes</p>
                            </div>
                        </div>
                        <div class="room-amenities">
                            @if($reservation->room->has_wifi)
                                <span class="amenity-tag"><i class="fas fa-wifi"></i> Wi-Fi</span>
                            @endif
                            @if($reservation->room->has_tv)
                                <span class="amenity-tag"><i class="fas fa-tv"></i> TV</span>
                            @endif
                            @if($reservation->room->has_air_conditioning)
                                <span class="amenity-tag"><i class="fas fa-wind"></i> Climatisation</span>
                            @endif
                            @if($reservation->room->has_minibar)
                                <span class="amenity-tag"><i class="fas fa-glass-cheers"></i> Mini-bar</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Dates du séjour --}}
                <div class="info-card">
                    <h4 class="info-title">
                        <i class="fas fa-calendar-alt"></i> Dates du séjour
                    </h4>
                    <div class="info-content">
                        <div class="dates-display">
                            <div class="date-display-item">
                                <span class="date-label">Arrivée</span>
                                <span class="date-value-large">{{ date('d/m/Y', strtotime($reservation->check_in_date)) }}</span>
                                <span class="date-time">À partir de 14h00</span>
                            </div>
                            <div class="date-display-item">
                                <span class="date-label">Départ</span>
                                <span class="date-value-large">{{ date('d/m/Y', strtotime($reservation->check_out_date)) }}</span>
                                <span class="date-time">Jusqu'à 12h00</span>
                            </div>
                        </div>
                        <div class="duration-display">
                            <span class="duration-label">Durée du séjour:</span>
                            <span class="duration-value">{{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date) }} nuit(s)</span>
                        </div>
                    </div>
                </div>
                
                {{-- Informations client --}}
                <div class="info-card">
                    <h4 class="info-title">
                        <i class="fas fa-user"></i> Informations client
                    </h4>
                    <div class="info-content">
                        <div class="info-row">
                            <span class="info-label">Nom:</span>
                            <span class="info-value">{{ $reservation->guest_name ?? Auth::user()->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $reservation->guest_email ?? Auth::user()->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Téléphone:</span>
                            <span class="info-value">{{ $reservation->guest_phone ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Adultes:</span>
                            <span class="info-value">{{ $reservation->number_of_adults }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Enfants:</span>
                            <span class="info-value">{{ $reservation->number_of_children ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                {{-- Prix total --}}
                <div class="info-card">
                    <h4 class="info-title">
                        <i class="fas fa-money-bill-wave"></i> Prix total
                    </h4>
                    <div class="info-content">
                        <div class="price-breakdown">
                            <div class="price-row">
                                <span>Chambre: {{ number_format($reservation->room->price_per_night, 0, ',', ' ') }} FCFA x {{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date) }} nuits</span>
                                <span>{{ number_format($reservation->room->price_per_night * \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date), 0, ',', ' ') }} FCFA</span>
                            </div>
                            
                            @if($reservation->services->count() > 0)
                                <div class="services-breakdown">
                                    <p class="services-title">Services ajoutés:</p>
                                    @foreach($reservation->services as $service)
                                        <div class="service-row">
                                            <span>{{ $service->service->name }}</span>
                                            <span>{{ number_format($service->price, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <div class="total-price">
                            <span>TOTAL</span>
                            <span class="total-amount">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <p class="tax-note">Taxes et frais inclus</p>
                    </div>
                </div>
            </div>
            
            {{-- Demandes spéciales --}}
            @if($reservation->special_requests)
                <div class="special-requests">
                    <h4 class="info-title">
                        <i class="fas fa-comment"></i> Demandes spéciales
                    </h4>
                    <div class="special-requests-content">
                        <p>{{ $reservation->special_requests }}</p>
                    </div>
                </div>
            @endif
            
            {{-- Boutons d'action --}}
            <div class="action-buttons">
                <a href="{{ route('booking.my-reservations') }}" class="btn-secondary">
                    <i class="fas fa-list"></i> Mes réservations
                </a>
                <a href="{{ route('home') }}" class="btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                @if(in_array($reservation->status, ['pending', 'confirmed']))
                    <button onclick="showCancelModal('{{ $reservation->id }}')" class="btn-cancel-lg">
                        <i class="fas fa-times"></i> Annuler la réservation
                    </button>
                @endif
            </div>
        </div>
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
    .page-header {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
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

    /* Success Message */
    .success-message-section {
        padding: 40px 0 0;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 30px;
    }

    .success-message i {
        font-size: 60px;
        margin-bottom: 20px;
    }

    .success-message h2 {
        font-size: 28px;
        margin-bottom: 10px;
    }

    /* Reservation Detail */
    .reservation-detail-section {
        padding: 0 0 60px;
    }

    .reservation-detail-card {
        background: var(--light);
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .reservation-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--primary);
    }

    .reservation-number {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .reservation-date {
        color: var(--text-light);
    }

    /* Status Badges */
    .status-badge-lg {
        color: var(--light);
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
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

    /* Info Grid */
    .reservation-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        margin-bottom: 40px;
    }

    .info-card {
        background: var(--gray);
        border-radius: 10px;
        padding: 20px;
    }

    .info-title {
        font-size: 18px;
        margin-bottom: 15px;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-title i {
        font-size: 20px;
    }

    /* Room Info */
    .room-info {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .room-image-small {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .room-image-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .room-number {
        font-size: 18px;
        margin-bottom: 5px;
    }

    .room-type {
        color: var(--text-light);
        margin-bottom: 5px;
    }

    .room-capacity {
        color: var(--text-light);
        font-size: 13px;
    }

    .room-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .amenity-tag {
        background: var(--light);
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Dates */
    .dates-display {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .date-display-item {
        flex: 1;
    }

    .date-display-item:first-child {
        padding-right: 20px;
        border-right: 1px solid #ddd;
    }

    .date-display-item:last-child {
        padding-left: 20px;
        text-align: right;
    }

    .date-label {
        color: var(--text-light);
        font-size: 14px;
        display: block;
        margin-bottom: 5px;
    }

    .date-value-large {
        font-size: 18px;
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
    }

    .date-time {
        color: var(--text-light);
        font-size: 13px;
    }

    .duration-display {
        text-align: center;
        padding-top: 10px;
        border-top: 1px dashed #ddd;
    }

    .duration-label {
        font-weight: 600;
        margin-right: 5px;
    }

    .duration-value {
        color: var(--primary);
        font-weight: 600;
    }

    /* Client Info */
    .info-row {
        display: flex;
        margin-bottom: 10px;
    }

    .info-label {
        width: 100px;
        color: var(--text-light);
    }

    .info-value {
        font-weight: 500;
    }

    /* Price */
    .price-breakdown {
        margin-bottom: 15px;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .services-breakdown {
        margin: 15px 0;
        padding: 10px 0;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }

    .services-title {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .service-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .total-price {
        display: flex;
        justify-content: space-between;
        font-size: 20px;
        font-weight: 700;
        color: var(--primary);
        margin-top: 15px;
    }

    .total-amount {
        font-size: 24px;
    }

    .tax-note {
        font-size: 12px;
        color: var(--text-light);
        margin-top: 5px;
    }

    /* Special Requests */
    .special-requests {
        margin-bottom: 30px;
    }

    .special-requests-content {
        background: var(--gray);
        border-radius: 10px;
        padding: 20px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn-cancel-lg {
        padding: 12px 25px;
        background: #dc3545;
        color: var(--light);
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-weight: 500;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cancel-lg:hover {
        background: #c82333;
        transform: translateY(-2px);
    }

    /* Modal (même style que my-reservations) */
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
        .reservation-info-grid {
            grid-template-columns: 1fr;
        }

        .reservation-detail-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .dates-display {
            flex-direction: column;
            gap: 15px;
        }

        .date-display-item:first-child {
            padding-right: 0;
            border-right: none;
        }

        .date-display-item:last-child {
            padding-left: 0;
            text-align: left;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-cancel-lg {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 32px;
        }

        .reservation-number {
            font-size: 20px;
        }

        .room-info {
            flex-direction: column;
        }

        .room-image-small {
            width: 100%;
            height: 150px;
        }
    }
</style>
@endpush
@endsection
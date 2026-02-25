{{-- resources/views/booking/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Réservation confirmée - SugnuHotel')

@section('content')
{{-- En-tête --}}
<section class="page-header" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; padding: 150px 0 80px;">
    <div class="container">
        <h1 class="page-title" style="color: var(--light); font-size: 48px; margin-bottom: 20px;">Réservation confirmée !</h1>
        <div class="breadcrumbs" style="color: rgba(255,255,255,0.8);">
            <a href="{{ route('home') }}" style="color: var(--light);">Accueil</a> / 
            <a href="{{ route('booking.my-reservations') }}" style="color: var(--light);">Mes réservations</a> / 
            <span>Confirmation</span>
        </div>
    </div>
</section>

{{-- Message de succès --}}
<section style="padding: 40px 0 0;">
    <div class="container">
        <div style="background: #d4edda; color: #155724; padding: 30px; border-radius: 15px; text-align: center; margin-bottom: 30px;">
            <i class="fas fa-check-circle" style="font-size: 60px; margin-bottom: 20px;"></i>
            <h2 style="font-size: 28px; margin-bottom: 10px;">Merci pour votre réservation !</h2>
            <p style="font-size: 18px;">Votre réservation a été confirmée avec succès.</p>
            <p style="font-size: 16px; margin-top: 15px;">Un email de confirmation vous a été envoyé à <strong>{{ $reservation->guest_email ?? Auth::user()->email }}</strong></p>
        </div>
    </div>
</section>

{{-- Détails de la réservation --}}
<section style="padding: 0 0 60px;">
    <div class="container">
        <div style="background: var(--light); border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            {{-- En-tête avec numéro de réservation --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid var(--primary);">
                <div>
                    <h3 style="font-size: 24px; margin-bottom: 5px;">Réservation #{{ $reservation->reservation_number }}</h3>
                    <p style="color: var(--text-light);">Effectuée le {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
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
                    <span style="background: '{{ $status['bg'] }}'; color: var(--light); padding: 8px 20px; border-radius: 30px; font-size: 14px; font-weight: 600;">
                        {{ $status['text'] }}
                    </span>
                </div>
            </div>
            
            {{-- Grille d'informations --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-bottom: 40px;">
                {{-- Informations chambre --}}
                <div>
                    <h4 style="font-size: 18px; margin-bottom: 15px; color: var(--primary);">
                        <i class="fas fa-bed"></i> Chambre réservée
                    </h4>
                    <div style="background: var(--gray); border-radius: 10px; padding: 20px;">
                        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <img src="{{ $reservation->room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                 alt="Chambre {{ $reservation->room->room_number }}"
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;">
                            <div>
                                <h5 style="font-size: 18px; margin-bottom: 5px;">Chambre {{ $reservation->room->room_number }}</h5>
                                <p style="color: var(--text-light);">{{ $reservation->room->roomType->name }}</p>
                                <p style="color: var(--text-light);">Capacité: {{ $reservation->room->max_occupancy }} personnes</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            @if($reservation->room->has_wifi)
                                <span style="background: var(--light); padding: 5px 10px; border-radius: 20px; font-size: 12px;"><i class="fas fa-wifi"></i> Wi-Fi</span>
                            @endif
                            @if($reservation->room->has_tv)
                                <span style="background: var(--light); padding: 5px 10px; border-radius: 20px; font-size: 12px;"><i class="fas fa-tv"></i> TV</span>
                            @endif
                            @if($reservation->room->has_air_conditioning)
                                <span style="background: var(--light); padding: 5px 10px; border-radius: 20px; font-size: 12px;"><i class="fas fa-wind"></i> Climatisation</span>
                            @endif
                            @if($reservation->room->has_minibar)
                                <span style="background: var(--light); padding: 5px 10px; border-radius: 20px; font-size: 12px;"><i class="fas fa-glass-cheers"></i> Mini-bar</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Dates du séjour --}}
                <div>
                    <h4 style="font-size: 18px; margin-bottom: 15px; color: var(--primary);">
                        <i class="fas fa-calendar-alt"></i> Dates du séjour
                    </h4>
                    <div style="background: var(--gray); border-radius: 10px; padding: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <div>
                                <p style="color: var(--text-light); font-size: 14px;">Arrivée</p>
                                <p style="font-size: 18px; font-weight: 600;">{{ date('d/m/Y', strtotime($reservation->check_in_date)) }}</p>
                                <p style="color: var(--text-light); font-size: 13px;">À partir de 14h00</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="color: var(--text-light); font-size: 14px;">Départ</p>
                                <p style="font-size: 18px; font-weight: 600;">{{ date('d/m/Y', strtotime($reservation->check_out_date)) }}</p>
                                <p style="color: var(--text-light); font-size: 13px;">Jusqu'à 12h00</p>
                            </div>
                        </div>
                        <div style="text-align: center; padding-top: 10px; border-top: 1px dashed #ddd;">
                            <p style="font-weight: 600;">Durée: <span style="color: var(--primary);">{{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date) }} nuit(s)</span></p>
                        </div>
                    </div>
                </div>
                
                {{-- Informations client --}}
                <div>
                    <h4 style="font-size: 18px; margin-bottom: 15px; color: var(--primary);">
                        <i class="fas fa-user"></i> Informations client
                    </h4>
                    <div style="background: var(--gray); border-radius: 10px; padding: 20px;">
                        <p><strong>Nom:</strong> {{ $reservation->guest_name ?? Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ $reservation->guest_email ?? Auth::user()->email }}</p>
                        <p><strong>Téléphone:</strong> {{ $reservation->guest_phone ?? 'Non renseigné' }}</p>
                        <p><strong>Adultes:</strong> {{ $reservation->number_of_adults }}</p>
                        <p><strong>Enfants:</strong> {{ $reservation->number_of_children ?? 0 }}</p>
                    </div>
                </div>
                
                {{-- Prix total --}}
                <div>
                    <h4 style="font-size: 18px; margin-bottom: 15px; color: var(--primary);">
                        <i class="fas fa-money-bill-wave"></i> Prix total
                    </h4>
                    <div style="background: var(--gray); border-radius: 10px; padding: 20px;">
                        <p style="font-size: 16px; margin-bottom: 10px;">
                            Chambre: {{ number_format($reservation->room->price_per_night, 0, ',', ' ') }} FCFA x {{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date) }} nuits
                        </p>
                        
                        @if($reservation->services->count() > 0)
                            <div style="margin: 15px 0; padding: 10px 0; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                                <p style="font-weight: 600; margin-bottom: 10px;">Services ajoutés:</p>
                                @foreach($reservation->services as $service)
                                    <p style="display: flex; justify-content: space-between; font-size: 14px;">
                                        <span>{{ $service->service->name }}</span>
                                        <span>{{ number_format($service->price, 0, ',', ' ') }} FCFA</span>
                                    </p>
                                @endforeach
                            </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: var(--primary); margin-top: 15px;">
                            <span>TOTAL</span>
                            <span>{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <p style="font-size: 12px; color: var(--text-light); margin-top: 5px;">Taxes et frais inclus</p>
                    </div>
                </div>
            </div>
            
            {{-- Demandes spéciales --}}
            @if($reservation->special_requests)
                <div style="margin-bottom: 30px; padding: 20px; background: var(--gray); border-radius: 10px;">
                    <h4 style="font-size: 18px; margin-bottom: 10px; color: var(--primary);">
                        <i class="fas fa-comment"></i> Demandes spéciales
                    </h4>
                    <p>{{ $reservation->special_requests }}</p>
                </div>
            @endif
            
            {{-- Boutons d'action --}}
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                <a href="{{ route('booking.my-reservations') }}" class="btn-secondary">
                    <i class="fas fa-list"></i> Mes réservations
                </a>
                <a href="{{ route('home') }}" class="btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                @if(in_array($reservation->status, ['pending', 'confirmed']))
                    <button onclick="showCancelModal('{{ $reservation->id }}')" class="btn-secondary" style="background: #dc3545; color: var(--light); border-color: #dc3545;">
                        <i class="fas fa-times"></i> Annuler la réservation
                    </button>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Modal d'annulation --}}
<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--light); border-radius: 15px; padding: 40px; max-width: 400px; width: 90%; position: relative;">
        <h3 style="font-size: 24px; margin-bottom: 15px;">Confirmer l'annulation</h3>
        <p style="color: var(--text-light); margin-bottom: 25px;">Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.</p>
        
        <form id="cancelForm" method="POST">
            @csrf
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

<style>
    @media (max-width: 768px) {
        .confirmation-section > .container > div {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
{{-- resources/views/booking/confirm.blade.php --}}
@extends('layouts.app')

@section('title', 'Confirmation de réservation - SugnuHotel')

@section('content')
{{-- En-tête --}}
<section class="page-header" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; padding: 150px 0 80px;">
    <div class="container">
        <h1 class="page-title" style="color: var(--light); font-size: 48px; margin-bottom: 20px;">Confirmation de réservation</h1>
        <div class="breadcrumbs" style="color: rgba(255,255,255,0.8);">
            <a href="{{ route('home') }}" style="color: var(--light);">Accueil</a> / 
            <a href="{{ route('booking.search') }}" style="color: var(--light);">Recherche</a> / 
            <span>Confirmation</span>
        </div>
    </div>
</section>

{{-- Contenu principal --}}
<section class="confirmation-section" style="padding: 60px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            {{-- Formulaire de confirmation --}}
            <div style="background: var(--light); border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <h2 style="font-size: 28px; margin-bottom: 30px;">Vos informations</h2>
                
                <form action="{{ route('booking.store') }}" method="POST">
                    @csrf
                    
                    {{-- Champs cachés pour les dates --}}
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="check_in" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">
                    <input type="hidden" name="children" value="{{ $children }}">
                    
                    {{-- Informations personnelles --}}
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 20px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--primary);">Coordonnées</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label for="first_name"><i class="fas fa-user"></i> Prénom</label>
                                <input type="text" id="first_name" name="first_name" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', Auth::user()->name ?? '') }}" required>
                                @error('first_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name"><i class="fas fa-user"></i> Nom</label>
                                <input type="text" id="last_name" name="last_name" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" id="email" name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', Auth::user()->email ?? '') }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> Téléphone</label>
                                <input type="tel" id="phone" name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" required>
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address"><i class="fas fa-map-marker-alt"></i> Adresse</label>
                            <textarea id="address" name="address" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    {{-- Services supplémentaires --}}
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 20px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--primary);">Services supplémentaires</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            @php
                                $services = \App\Models\Service::where('is_active', true)->get();
                            @endphp
                            
                            @foreach($services as $service)
                                <div class="service-checkbox" style="background: var(--gray); border-radius: 10px; padding: 15px; transition: var(--transition);">
                                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" 
                                               class="service-input" 
                                               data-price="{{ $service->price }}"
                                               onchange="updateTotalPrice()">
                                        <div>
                                            <strong>{{ $service->name }}</strong>
                                            <p style="font-size: 13px; color: var(--text-light); margin: 0;">{{ number_format($service->price, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Demandes spéciales --}}
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 20px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--primary);">Demandes spéciales</h3>
                        
                        <div class="form-group">
                            <textarea id="special_requests" name="special_requests" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="N'hésitez pas à nous faire part de vos demandes particulières (allergies, préférences, occasions spéciales...)"></textarea>
                        </div>
                    </div>
                    
                    {{-- Conditions --}}
                    <div style="margin-bottom: 30px;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <input type="checkbox" id="terms" name="terms" required style="margin-top: 3px;">
                            <label for="terms" style="font-size: 14px;">
                                J'accepte les <a href="#" style="color: var(--primary);">conditions générales de vente</a> et la 
                                <a href="#" style="color: var(--primary);">politique d'annulation</a> de l'établissement.
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
                        <i class="fas fa-check-circle"></i> Confirmer ma réservation
                    </button>
                </form>
            </div>
            
            {{-- Résumé de la réservation --}}
            <div style="background: var(--light); border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); height: fit-content; position: sticky; top: 100px;">
                <h3 style="font-size: 22px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid var(--primary);">Récapitulatif</h3>
                
                {{-- Image chambre --}}
                <div style="border-radius: 10px; overflow: hidden; margin-bottom: 20px;">
                    <img src="{{ $room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                         alt="{{ $room->room_number }}" style="width: 100%; height: 150px; object-fit: cover;">
                </div>
                
                {{-- Détails chambre --}}
                <div style="margin-bottom: 20px;">
                    <h4 style="font-size: 18px; margin-bottom: 5px;">Chambre {{ $room->room_number }}</h4>
                    <p style="color: var(--text-light); font-size: 14px; margin-bottom: 5px;">{{ $room->roomType->name }}</p>
                    <p style="color: var(--text-light); font-size: 14px;">Capacité: {{ $room->max_occupancy }} personnes</p>
                </div>
                
                {{-- Détails séjour --}}
                                <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Arrivée</span>
                        <strong>{{ date('d/m/Y', strtotime($checkIn)) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Départ</span>
                        <strong>{{ date('d/m/Y', strtotime($checkOut)) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Durée</span>
                        <strong>{{ $nights }} nuit(s)</strong>
                    </div>
                </div>
                
                {{-- Prix --}}
                <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Chambre ({{ $nights }} nuits)</span>
                        <strong>{{ number_format($room->price_per_night * $nights, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    
                    <div id="services-list" style="margin-top: 10px;">
                        {{-- Les services sélectionnés apparaîtront ici via JS --}}
                    </div>
                </div>
                
                {{-- Total --}}
                <div style="margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 18px; font-weight: 600;">Total</span>
                        <span id="total-price" style="font-size: 28px; font-weight: 700; color: var(--primary);">
                            {{ number_format($room->price_per_night * $nights, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <p style="font-size: 13px; color: var(--text-light); margin-top: 5px;">Taxes et frais inclus</p>
                </div>
                
                {{-- Politique d'annulation --}}
                <div style="background: var(--gray); border-radius: 10px; padding: 15px;">
                    <h5 style="font-size: 16px; margin-bottom: 10px;"><i class="fas fa-info-circle" style="color: var(--primary);"></i> Politique d'annulation</h5>
                    <p style="font-size: 13px; color: var(--text-light); margin: 0;">
                        Annulation gratuite jusqu'à 24h avant l'arrivée. Passé ce délai, le montant de la première nuit sera facturé.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    const roomPrice = '{{ $room -> price_per_night }}';
    const nights = '{{ $nights }}';
    let basePrice = roomPrice * nights;
    
    function updateTotalPrice() {
        const checkboxes = document.querySelectorAll('.service-input:checked');
        let servicesPrice = 0;
        let servicesHtml = '';
        
        checkboxes.forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price);
            servicesPrice += price;
            
            // Trouver le nom du service (à adapter selon votre structure)
            const serviceDiv = checkbox.closest('.service-checkbox');
            const serviceName = serviceDiv.querySelector('strong').textContent;
            
            servicesHtml += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px;">
                    <span>${serviceName}</span>
                    <strong>${price.toLocaleString('fr-FR')} FCFA</strong>
                </div>
            `;
        });
        
        const total = basePrice + servicesPrice;
        
        document.getElementById('total-price').textContent = total.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('services-list').innerHTML = servicesHtml;
        
        if (servicesHtml) {
            document.getElementById('services-list').innerHTML = '<div style="margin-top: 10px;"><h5 style="font-size: 14px; margin-bottom: 10px;">Services ajoutés:</h5>' + servicesHtml + '</div>';
        } else {
            document.getElementById('services-list').innerHTML = '';
        }
    }
    
    // Animation pour les services
    document.querySelectorAll('.service-checkbox').forEach(div => {
        div.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
        
        div.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
</script>
@endpush

<style>
    .service-checkbox:hover {
        background: var(--primary) !important;
    }
    
    .service-checkbox:hover label {
        color: var(--light);
    }
    
    .service-checkbox:hover p {
        color: rgba(255,255,255,0.8) !important;
    }
    
    @media (max-width: 768px) {
        .confirmation-section > .container > div {
            grid-template-columns: 1fr;
        }
        
        .confirmation-section > .container > div > div:last-child {
            position: static !important;
        }
    }
</style>
@endsection
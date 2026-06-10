{{-- resources/views/rooms/show.blade.php --}}
@extends('layouts.app')

@section('title', $room->room_number . ' - ' . $room->roomType->name . ' | SugnuHotel')

@push('styles')
<style>
    /* Styles supplémentaires pour la page chambre */
    .room-gallery {
        margin-top: 80px;
    }
    
    .room-gallery-main {
        height: 500px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .room-gallery-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .room-gallery-thumbs {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-top: 15px;
    }
    
    .room-gallery-thumb {
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.6;
        transition: var(--transition);
        border: 2px solid transparent;
    }
    
    .room-gallery-thumb:hover,
    .room-gallery-thumb.active {
        opacity: 1;
        border-color: var(--primary);
    }
    
    .room-gallery-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .room-info-card {
        background: var(--light);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    
    .room-price-badge {
        background: var(--primary);
        color: var(--light);
        padding: 10px 25px;
        border-radius: 50px;
        display: inline-block;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .room-price-badge span {
        font-size: 14px;
        font-weight: 400;
        opacity: 0.9;
    }
    
    .room-features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 30px 0;
    }
    
    .room-feature {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .room-feature i {
        width: 50px;
        height: 50px;
        background: var(--primary);
        color: var(--light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .room-feature h4 {
        font-size: 16px;
        margin-bottom: 5px;
        color: var(--dark);
    }
    
    .room-feature p {
        font-size: 14px;
        color: var(--text-light);
    }
    
    .booking-sidebar {
        background: var(--light);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        position: sticky;
        top: 100px;
    }
    
    .booking-sidebar h3 {
        font-size: 22px;
        margin-bottom: 20px;
        color: var(--dark);
        border-bottom: 2px solid var(--primary);
        padding-bottom: 10px;
    }
    
    .price-calculation {
        background: var(--gray);
        padding: 20px;
        border-radius: 15px;
        margin: 20px 0;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: var(--text);
    }
    
    .price-row.total {
        font-weight: 700;
        font-size: 20px;
        color: var(--primary);
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 10px;
    }
    
    .service-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 10px;
        transition: var(--transition);
    }
    
    .service-checkbox:hover {
        border-color: var(--primary);
        background: rgba(196,162,122,0.05);
    }
    
    .service-checkbox input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: var(--primary);
    }
    
    .service-info {
        flex: 1;
    }
    
    .service-info h4 {
        font-size: 16px;
        margin-bottom: 3px;
        color: var(--dark);
    }
    
    .service-info p {
        font-size: 13px;
        color: var(--text-light);
    }
    
    .service-price {
        font-weight: 600;
        color: var(--primary);
    }
    
    .btn-book-now {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: var(--light);
        border: none;
        border-radius: 10px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 20px;
    }
    
    .btn-book-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(196,162,122,0.3);
    }
    
    .similar-room-card {
        background: var(--light);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: var(--transition);
    }
    
    .similar-room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .similar-room-image {
        height: 150px;
        overflow: hidden;
    }
    
    .similar-room-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }
    
    .similar-room-card:hover .similar-room-image img {
        transform: scale(1.1);
    }
    
    .similar-room-details {
        padding: 15px;
    }
    
    .similar-room-details h4 {
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .similar-room-details .price {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
    }
</style>
@endpush

@section('content')
<div class="room-gallery">
    <div class="container">
        <div class="row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            {{-- Colonne gauche: Galerie et infos --}}
            <div>
                {{-- Fil d'Ariane --}}
                <div style="margin-bottom: 20px;">
                    <a href="{{ route('home') }}" style="color: var(--primary);">Accueil</a> 
                    <i class="fas fa-chevron-right" style="margin: 0 10px; font-size: 12px;"></i>
                    <a href="{{ route('rooms') }}" style="color: var(--primary);">Nos chambres</a>
                    <i class="fas fa-chevron-right" style="margin: 0 10px; font-size: 12px;"></i>
                    <span style="color: var(--text-light);">Chambre {{ $room->room_number }}</span>
                </div>

                {{-- Galerie principale --}}
                <div class="room-gallery-main" id="mainImage">
                    @if($room->images && $room->images->count() > 0)
                        <img src="{{ asset('storage/' . $room->images->first()->image_path) }}" alt="Chambre {{ $room->room_number }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Chambre">
                    @endif
                </div>
                
                {{-- Miniatures --}}
                @if($room->images && $room->images->count() > 1)
                <div class="room-gallery-thumbs">
                    @foreach($room->images as $index => $image)
                    <div class="room-gallery-thumb {{ $index === 0 ? 'active' : '' }}" data-image="{{ asset('storage/' . $image->image_path) }}" onclick="changeImage(this.dataset.image, this)">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Miniature">
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Informations détaillées --}}
                <div class="room-info-card">
                    <h1 style="font-size: 36px; margin-bottom: 10px;">Chambre {{ $room->room_number }}</h1>
                    <p style="color: var(--primary); font-size: 18px; margin-bottom: 20px;">{{ $room->roomType->name }}</p>
                    
                    <div style="margin-bottom: 30px;">
                        <span class="room-price-badge">
                            {{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA <span>/nuit</span>
                        </span>
                    </div>
                    
                    <h3 style="font-size: 22px; margin-bottom: 15px;">Description</h3>
                    <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 30px;">
                        {{ $room->roomType->description ?? 'Découvrez le confort et l\'élégance de cette chambre, parfaitement équipée pour rendre votre séjour inoubliable.' }}
                    </p>
                    
                    <h3 style="font-size: 22px; margin-bottom: 15px;">Équipements</h3>
                    <div class="room-features-grid">
                        <div class="room-feature">
                            <i class="fas fa-user-friends"></i>
                            <div>
                                <h4>Capacité</h4>
                                <p>{{ $room->max_occupancy }} personnes maximum</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-arrows-alt"></i>
                            <div>
                                <h4>Surface</h4>
                                <p>30 m²</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-wifi"></i>
                            <div>
                                <h4>Wi-Fi</h4>
                                <p>Gratuit et haut débit</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-tv"></i>
                            <div>
                                <h4>Télévision</h4>
                                <p>Écran plat LED</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-snowflake"></i>
                            <div>
                                <h4>Climatisation</h4>
                                <p>Individuelle réglable</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-coffee"></i>
                            <div>
                                <h4>Machine à café</h4>
                                <p>Nespresso</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-bath"></i>
                            <div>
                                <h4>Salle de bain</h4>
                                <p>Douche à l'italienne</p>
                            </div>
                        </div>
                        
                        <div class="room-feature">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <h4>Sécurité</h4>
                                <p>Coffre-fort</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Colonne droite: Formulaire de réservation --}}
            <div>
                <div class="booking-sidebar">
                    <h3>Réserver cette chambre</h3>
                    
                    <form id="bookingForm" method="GET" action="{{ route('booking.search', $room->id) }}">
                        @csrf
                        
                        {{-- Dates --}}
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Date d'arrivée</label>
                            <input type="date" name="check_in" id="check_in" 
                                   min="{{ date('Y-m-d') }}" 
                                   value="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                   class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Date de départ</label>
                            <input type="date" name="check_out" id="check_out" 
                                   min="{{ date('Y-m-d', strtotime('+2 days')) }}" 
                                   value="{{ date('Y-m-d', strtotime('+3 days')) }}" 
                                   class="form-control" required>
                        </div>
                        
                        {{-- Nombre de personnes --}}
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Adultes</label>
                            <select name="adults" class="form-control" required>
                                @for($i = 1; $i <= $room->max_occupancy; $i++)
                                    <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} adulte(s)</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-child"></i> Enfants</label>
                            <select name="children" class="form-control">
                                @for($i = 0; $i <= 3; $i++)
                                    <option value="{{ $i }}">{{ $i }} enfant(s)</option>
                                @endfor
                            </select>
                        </div>
                        
                        {{-- Calcul du prix --}}
                        <div class="price-calculation" id="priceCalculation">
                            @php
                                $nights = 2; // À calculer dynamiquement avec JS
                                $roomPrice = $room->price_per_night;
                            @endphp
                            <div class="price-row">
                                <span>Prix par nuit</span>
                                <span>{{ number_format($roomPrice, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="price-row">
                                <span>Nombre de nuits</span>
                                <span id="nightsCount">2</span>
                            </div>
                            <div class="price-row total">
                                <span>Total</span>
                                <span id="totalPrice">{{ number_format($roomPrice * 2, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-book-now">
                            <i class="fas fa-calendar-check"></i> Vérifier la disponibilité
                        </button>
                    </form>
                </div>
                
                {{-- Chambres similaires --}}
                @if(isset($similarRooms) && $similarRooms->count() > 0)
                <div style="margin-top: 30px;">
                    <h3 style="font-size: 20px; margin-bottom: 20px;">Chambres similaires</h3>
                    <div style="display: grid; gap: 15px;">
                        @foreach($similarRooms as $similar)
                        <a href="{{ route('room.show', $similar->id) }}" class="similar-room-card" style="text-decoration: none;">
                            <div class="similar-room-image">
                                @if($similar->images && $similar->images->count() > 0)
                                    <img src="{{ asset('storage/' . $similar->images->first()->image_path) }}" alt="Chambre">
                                @else
                                    <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Chambre">
                                @endif
                            </div>
                            <div class="similar-room-details">
                                <h4 style="color: var(--dark);">Chambre {{ $similar->room_number }}</h4>
                                <div class="price">{{ number_format($similar->price_per_night, 0, ',', ' ') }} FCFA <span style="font-size: 12px;">/nuit</span></div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<div
    id="room-data"
    data-price="{{ $room->price_per_night ?? 0 }}"
></div>


@push('scripts')
<script>
    function changeImage(src, element) {
        document.getElementById('mainImage').innerHTML =
            '<img src="' + src + '" alt="Chambre" style="width:100%; height:100%; object-fit:cover;">';

        document.querySelectorAll('.room-gallery-thumb').forEach(function (thumb) {
            thumb.classList.remove('active');
        });

        element.classList.add('active');
    }

    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const nightsSpan = document.getElementById('nightsCount');
    const totalSpan = document.getElementById('totalPrice');
    const roomPrice = Number(
    document.getElementById('room-data').dataset.price );

    function updatePrice() {
        if (!checkIn.value || !checkOut.value) return;

        const checkInDate = new Date(checkIn.value);
        const checkOutDate = new Date(checkOut.value);

        if (checkOutDate > checkInDate) {
            const diffTime = checkOutDate - checkInDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            nightsSpan.textContent = diffDays;
            totalSpan.textContent =
                (roomPrice * diffDays).toLocaleString('fr-FR') + ' FCFA';
        }
    }

    checkIn.addEventListener('change', function () {
        const minDate = new Date(this.value);
        minDate.setDate(minDate.getDate() + 1);
        checkOut.min = minDate.toISOString().split('T')[0];
        updatePrice();
    });

    checkOut.addEventListener('change', updatePrice);
    updatePrice();
</script>
@endpush

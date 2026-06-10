{{-- resources/views/rooms/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Nos chambres - SugnuHotel')

@section('content')
{{-- En-tête de la page --}}
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Nos chambres</h1>
        <div class="breadcrumbs">
            <a href="{{ route('home') }}">Accueil</a> / 
            <span>Chambres</span>
        </div>
    </div>
</section>

{{-- Formulaire de recherche rapide --}}
<section class="quick-search-section">
    <div class="container">
        <div class="quick-search-card">
            <h2>Rechercher une chambre</h2>
            <form action="{{ route('booking.search') }}" method="GET" class="quick-search-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="check_in"><i class="fas fa-calendar-alt"></i> Arrivée</label>
                        <input type="date" id="check_in" name="check_in" 
                               min="{{ date('Y-m-d') }}" 
                               value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="check_out"><i class="fas fa-calendar-alt"></i> Départ</label>
                        <input type="date" id="check_out" name="check_out" 
                               min="{{ date('Y-m-d', strtotime('+2 days')) }}" 
                               value="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="adults"><i class="fas fa-user"></i> Adultes</label>
                        <select id="adults" name="adults" required>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} {{ $i > 1 ? 'Adultes' : 'Adulte' }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="children"><i class="fas fa-child"></i> Enfants</label>
                        <select id="children" name="children">
                            @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'Enfants' : 'Enfant' }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> Vérifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Filtres et tri --}}
<section class="filters-section">
    <div class="container">
        <div class="filters-header">
            <h2 class="section-title">Toutes nos chambres</h2>
            <p class="section-description">{{ $rooms->total() }} chambres disponibles</p>
        </div>
        
        <div class="filters-toolbar">
            <div class="filter-dropdowns">
                <select id="capacity-filter" class="filter-select">
                    <option value="">Capacité</option>
                    <option value="1">1 personne</option>
                    <option value="2">2 personnes</option>
                    <option value="3">3 personnes</option>
                    <option value="4">4 personnes</option>
                </select>
                
                <select id="type-filter" class="filter-select">
                    <option value="">Type de chambre</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                
                <select id="price-filter" class="filter-select">
                    <option value="">Prix</option>
                    <option value="0-50000">Moins de 50 000 FCFA</option>
                    <option value="50000-100000">50 000 - 100 000 FCFA</option>
                    <option value="100000-150000">100 000 - 150 000 FCFA</option>
                    <option value="150000+">Plus de 150 000 FCFA</option>
                </select>
            </div>
            
            <div class="sort-options">
                <label for="sort">Trier par :</label>
                <select id="sort" class="sort-select" onchange="applySort()">
                    <option value="default">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                    <option value="capacity_asc">Capacité croissante</option>
                    <option value="capacity_desc">Capacité décroissante</option>
                </select>
            </div>
        </div>
    </div>
</section>

{{-- Grille des chambres --}}
<section class="rooms-grid-section">
    <div class="container">
        @if($rooms->count() > 0)
            <div class="rooms-grid">
                @foreach($rooms as $room)
                    <div class="room-card">
                        <div class="room-image">
                            <img src="{{ $room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                 alt="Chambre {{ $room->room_number }}">
                            <span class="room-category">{{ $room->roomType->name }}</span>
                            @if($room->status == 'available')
                                <span class="availability-badge available">
                                    <i class="fas fa-check-circle"></i> Disponible
                                </span>
                            @else
                                <span class="availability-badge occupied">
                                    <i class="fas fa-times-circle"></i> Occupée
                                </span>
                            @endif
                        </div>
                        
                        <div class="room-details">
                            <h3 class="room-title">Chambre {{ $room->room_number }}</h3>
                            <p class="room-description">{{ $room->roomType->description ?? 'Chambre confortable avec toutes les commodités' }}</p>
                            
                            <div class="room-features">
                                <span><i class="fas fa-user-friends"></i> {{ $room->max_occupancy }} pers.</span>
                                <span><i class="fas fa-arrows-alt"></i> {{ $room->size ?? 30 }} m²</span>
                                <span><i class="fas fa-wifi"></i> Wi-Fi</span>
                            </div>
                            
                            <div class="room-amenities">
                                @if($room->has_air_conditioning)
                                    <span class="amenity-tooltip" title="Climatisation">
                                        <i class="fas fa-wind"></i>
                                    </span>
                                @endif
                                @if($room->has_tv)
                                    <span class="amenity-tooltip" title="Télévision">
                                        <i class="fas fa-tv"></i>
                                    </span>
                                @endif
                                @if($room->has_minibar)
                                    <span class="amenity-tooltip" title="Mini-bar">
                                        <i class="fas fa-glass-cheers"></i>
                                    </span>
                                @endif
                                @if($room->has_balcony)
                                    <span class="amenity-tooltip" title="Balcon">
                                        <i class="fas fa-door-open"></i>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="room-price">
                                <div class="price">
                                    {{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA
                                    <span>/nuit</span>
                                </div>
                                <a href="{{ route('room.show', $room->id) }}" class="btn-details">
                                    Détails <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
           
        @else
            <div class="no-rooms">
                <i class="fas fa-bed no-rooms-icon"></i>
                <h3>Aucune chambre disponible</h3>
                <p>Veuillez réessayer plus tard ou modifier vos critères de recherche.</p>
                <a href="{{ route('home') }}" class="btn-primary">Retour à l'accueil</a>
            </div>
        @endif
    </div>
        {{-- Pagination --}}
            <div class="pagination-wrapper">
                {{ $rooms->appends(request()->query())->links() }}
            </div>
</section>



{{-- Section d'appel à l'action --}}
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Prêt pour un séjour inoubliable ?</h2>
        <p class="cta-description">Réservez dès maintenant et profitez de nos meilleurs tarifs</p>
        <a href="{{ route('home') }}#booking" class="btn-cta">
            <i class="fas fa-calendar-check"></i> Réserver maintenant
        </a>
    </div>
</section>

@push('scripts')
<script>
    // Validation des dates
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    
    if (checkIn && checkOut) {
        checkIn.addEventListener('change', function() {
            const minDate = new Date(this.value);
            minDate.setDate(minDate.getDate() + 1);
            checkOut.min = minDate.toISOString().split('T')[0];
            
            if (checkOut.value < this.value) {
                checkOut.value = minDate.toISOString().split('T')[0];
            }
        });
    }
    
    // Fonction pour appliquer les filtres
    function applyFilters() {
        const capacity = document.getElementById('capacity-filter').value;
        const type = document.getElementById('type-filter').value;
        const price = document.getElementById('price-filter').value;
        const sort = document.getElementById('sort').value;
        
        let url = new URL(window.location.href);
        
        if (capacity) url.searchParams.set('capacity', capacity);
        else url.searchParams.delete('capacity');
        
        if (type) url.searchParams.set('room_type', type);
        else url.searchParams.delete('room_type');
        
        if (price) {
            const [min, max] = price.split('-');
            url.searchParams.set('price_min', min);
            if (max) url.searchParams.set('price_max', max);
            else url.searchParams.delete('price_max');
        } else {
            url.searchParams.delete('price_min');
            url.searchParams.delete('price_max');
        }
        
        if (sort && sort !== 'default') url.searchParams.set('sort', sort);
        else url.searchParams.delete('sort');
        
        window.location.href = url.toString();
    }
    
    // Fonction pour le tri
    function applySort() {
        applyFilters();
    }
    
    // Écouteurs d'événements pour les filtres
    document.getElementById('capacity-filter')?.addEventListener('change', applyFilters);
    document.getElementById('type-filter')?.addEventListener('change', applyFilters);
    document.getElementById('price-filter')?.addEventListener('change', applyFilters);
</script>
@endpush

@push('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        padding: 150px 0 80px;
    }

    .page-title {
        color: var(--light);
        font-size: 48px;
        margin-bottom: 20px;
        text-align: center;
    }

    .breadcrumbs {
        color: rgba(255,255,255,0.8);
        text-align: center;
    }

    .breadcrumbs a {
        color: var(--light);
        text-decoration: none;
    }

    .breadcrumbs a:hover {
        color: var(--primary);
    }

    /* Quick Search */
    .quick-search-section {
        position: relative;
        margin-top: -50px;
        z-index: 10;
    }

    .quick-search-card {
        background: var(--light);
        border-radius: 15px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        padding: 30px;
    }

    .quick-search-card h2 {
        text-align: center;
        margin-bottom: 20px;
        color: var(--dark);
        font-size: 24px;
    }

    .quick-search-form {
        width: 100%;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto;
        gap: 15px;
        align-items: end;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: var(--text-light);
        font-size: 13px;
        font-weight: 500;
    }

    .form-group label i {
        color: var(--primary);
        margin-right: 5px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 14px;
        transition: var(--transition);
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(196,162,122,0.1);
    }

    .btn-search {
        padding: 10px 25px;
        background: var(--primary);
        color: var(--light);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        height: 42px;
        white-space: nowrap;
    }

    .btn-search:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(196,162,122,0.3);
    }

    /* Filters Section */
    .filters-section {
        padding: 60px 0 30px;
    }

    .filters-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 36px;
        color: var(--dark);
        margin-bottom: 10px;
    }

    .section-description {
        color: var(--text-light);
    }

    .filters-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        background: var(--light);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .filter-dropdowns {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-select,
    .sort-select {
        padding: 10px 15px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 14px;
        min-width: 150px;
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-select:hover,
    .sort-select:hover {
        border-color: var(--primary);
    }

    .sort-options {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sort-options label {
        color: var(--text-light);
        font-size: 14px;
    }

    /* Rooms Grid */
    .rooms-grid-section {
        padding: 0 0 60px;
    }

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    .room-card {
        background: var(--light);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: var(--transition);
    }

    .room-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .room-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .room-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .room-card:hover .room-image img {
        transform: scale(1.1);
    }

    .room-category {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--primary);
        color: var(--light);
        padding: 5px 15px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
        z-index: 2;
    }

    .availability-badge {
        position: absolute;
        bottom: 20px;
        left: 20px;
        padding: 5px 15px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
        z-index: 2;
    }

    .availability-badge.available {
        background: #28a745;
        color: var(--light);
    }

    .availability-badge.occupied {
        background: #dc3545;
        color: var(--light);
    }

    .room-details {
        padding: 25px;
    }

    .room-title {
        font-size: 22px;
        margin-bottom: 10px;
        color: var(--dark);
    }

    .room-description {
        color: var(--text-light);
        font-size: 14px;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .room-features {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .room-features span {
        font-size: 13px;
        color: var(--text-light);
    }

    .room-features i {
        color: var(--primary);
        margin-right: 5px;
    }

    .room-amenities {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .amenity-tooltip {
        width: 30px;
        height: 30px;
        background: var(--gray);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 14px;
        cursor: help;
        transition: var(--transition);
    }

    .amenity-tooltip:hover {
        background: var(--primary);
        color: var(--light);
    }

    .room-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price {
        font-size: 22px;
        font-weight: 700;
        color: var(--primary);
    }

    .price span {
        font-size: 13px;
        font-weight: 400;
        color: var(--text-light);
    }

    .btn-details {
        padding: 8px 20px;
        background: var(--primary);
        color: var(--light);
        text-decoration: none;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-details:hover {
        background: var(--primary-dark);
        transform: translateX(5px);
    }

 /* Pagination - Version corrigée avec flèches alignées */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center; /* Important pour aligner verticalement */
    }

    .pagination li {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pagination li a,
    .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        background: var(--light);
        color: var(--text);
        text-decoration: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        border: 1px solid #e1e1e1;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        line-height: 1; /* Uniformiser la hauteur de ligne */
    }

    .pagination li a:hover {
        background: var(--primary);
        color: var(--light);
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(196,162,122,0.2);
    }

    .pagination li.active span {
        background: var(--primary);
        color: var(--light);
        border-color: var(--primary);
        font-weight: 600;
    }

    .pagination li.disabled span {
        background: var(--gray);
        color: var(--text-light);
        cursor: not-allowed;
        opacity: 0.5;
        border-color: #e1e1e1;
    }

    /* Boutons Précédent/Suivant - CORRIGÉ */
    .pagination li:first-child a,
    .pagination li:first-child span,
    .pagination li:last-child a,
    .pagination li:last-child span {
        padding: 0 12px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Style des icônes - Plus petites et alignées */
    .pagination li:first-child a i,
    .pagination li:last-child a i,
    .pagination li:first-child span i,
    .pagination li:last-child span i {
        font-size: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    /* Pour les boutons "Précédent" et "Suivant" complets */
    .pagination li:first-child a span,
    .pagination li:last-child a span,
    .pagination li:first-child span span,
    .pagination li:last-child span span {
        display: inline-block;
        line-height: 1;
    }

    /* Cacher le texte sur mobile */
    @media (max-width: 480px) {
        .pagination li:first-child a span,
        .pagination li:last-child a span,
        .pagination li:first-child span span,
        .pagination li:last-child span span {
            display: none;
        }
        
        .pagination li:first-child a,
        .pagination li:last-child a,
        .pagination li:first-child span,
        .pagination li:last-child span {
            padding: 0 8px;
            min-width: 28px;
            height: 28px;
        }
        
        .pagination li a,
        .pagination li span {
            min-width: 28px;
            height: 28px;
            font-size: 12px;
        }
    }
    /* /////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////// */

    /* Cacher le texte sur mobile */
    @media (max-width: 480px) {
        .pagination li:first-child a span,
        .pagination li:last-child a span {
            display: none;
        }
        
        .pagination li a,
        .pagination li span {
            min-width: 28px;
            height: 28px;
            font-size: 12px;
        }
    }

        .btn-cta:hover {
            background: var(--light);
            color: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .rooms-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 36px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-dropdowns {
                flex-direction: column;
            }
            
            .filter-select,
            .sort-select {
                width: 100%;
            }
            
            .sort-options {
                justify-content: space-between;
            }
            
            .cta-title {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 28px;
            }
            
            .quick-search-card {
                padding: 20px;
            }
            
            .room-price {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .btn-details {
                width: 100%;
                justify-content: center;
            }
        }
</style>
@endpush
@endsection
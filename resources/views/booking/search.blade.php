{{-- resources/views/booking/search.blade.php --}}
@extends('layouts.app')

@section('title', 'Résultats de recherche - SugnuHotel')

@section('content')
{{-- En-tête de la page --}}
<section class="page-header" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; padding: 150px 0 80px;">
    <div class="container">
        <h1 class="page-title" style="color: var(--light); font-size: 48px; margin-bottom: 20px;">Résultats de recherche</h1>
        <div class="breadcrumbs" style="color: rgba(255,255,255,0.8);">
            <a href="{{ route('home') }}" style="color: var(--light);">Accueil</a> / 
            <span>Recherche</span>
        </div>
    </div>
</section>

{{-- Résumé de la recherche --}}
<section class="search-summary" style="padding: 40px 0; background: var(--gray);">
    <div class="container">
        <div class="search-summary-card" style="background: var(--light); border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h3 style="font-size: 24px; margin-bottom: 10px;">Votre recherche</h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <span><i class="fas fa-calendar-alt" style="color: var(--primary); margin-right: 8px;"></i> Du {{ date('d/m/Y', strtotime(request('check_in'))) }}</span>
                        <span><i class="fas fa-calendar-alt" style="color: var(--primary); margin-right: 8px;"></i> Au {{ date('d/m/Y', strtotime(request('check_out'))) }}</span>
                        <span><i class="fas fa-user" style="color: var(--primary); margin-right: 8px;"></i> {{ request('adults') }} adulte(s)</span>
                        @if(request('children') > 0)
                            <span><i class="fas fa-child" style="color: var(--primary); margin-right: 8px;"></i> {{ request('children') }} enfant(s)</span>
                        @endif
                    </div>
                </div>
                <a href="#modify-search" class="btn-secondary" style="padding: 12px 25px;" onclick="toggleModifySearch()">Modifier la recherche</a>
            </div>
            
            {{-- Formulaire de modification caché --}}
            <div id="modify-search" style="display: none; margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                <h4 style="margin-bottom: 20px;">Modifier votre recherche</h4>
                <form action="{{ route('booking.search') }}" method="GET" class="booking-form-grid">
                    <div class="form-group">
                        <label for="check_in"><i class="fas fa-calendar-alt"></i> Arrivée</label>
                        <input type="date" id="check_in" name="check_in" 
                               min="{{ date('Y-m-d') }}" 
                               value="{{ request('check_in') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="check_out"><i class="fas fa-calendar-alt"></i> Départ</label>
                        <input type="date" id="check_out" name="check_out" 
                               min="{{ date('Y-m-d', strtotime(request('check_in').' +1 day')) }}" 
                               value="{{ request('check_out') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="adults"><i class="fas fa-user"></i> Adultes</label>
                        <select id="adults" name="adults" required>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('adults') == $i ? 'selected' : '' }}>{{ $i }} {{ $i > 1 ? 'Adultes' : 'Adulte' }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="children"><i class="fas fa-child"></i> Enfants</label>
                        <select id="children" name="children">
                            @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}" {{ request('children') == $i ? 'selected' : '' }}>{{ $i }} {{ $i > 1 ? 'Enfants' : 'Enfant' }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Résultats --}}
<section class="search-results" style="padding: 60px 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 32px;">{{ $availableRooms->count() }} chambre(s) disponible(s)</h2>
            
            {{-- Filtres et tri --}}
            <div style="display: flex; gap: 15px;">
                <select id="sort" class="form-control" style="width: 200px;" onchange="applyFilters()">
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                    <option value="capacity_asc" {{ request('sort') == 'capacity_asc' ? 'selected' : '' }}>Capacité croissante</option>
                    <option value="capacity_desc" {{ request('sort') == 'capacity_desc' ? 'selected' : '' }}>Capacité décroissante</option>
                </select>
                
                <button class="btn-secondary" style="padding: 12px 25px;" onclick="toggleFilters()">
                    <i class="fas fa-filter"></i> Filtres
                </button>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 30px;">
            {{-- Filtres latéraux --}}
            <aside class="filters-sidebar" style="background: var(--light); border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); height: fit-content;">
                <h4 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid var(--primary);">Filtres</h4>
                
                <form action="{{ route('booking.search') }}" method="GET" id="filter-form">
                    <input type="hidden" name="check_in" value="{{ request('check_in') }}">
                    <input type="hidden" name="check_out" value="{{ request('check_out') }}">
                    <input type="hidden" name="adults" value="{{ request('adults') }}">
                    <input type="hidden" name="children" value="{{ request('children') }}">
                    <input type="hidden" name="sort" id="sort-input" value="{{ request('sort') }}">
                    
                    {{-- Prix --}}
                    <div class="filter-group" style="margin-bottom: 25px;">
                        <h5 style="margin-bottom: 15px;">Prix par nuit</h5>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" style="padding: 10px;">
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" style="padding: 10px;">
                        </div>
                    </div>
                    
                    {{-- Type de chambre --}}
                    <div class="filter-group" style="margin-bottom: 25px;">
                        <h5 style="margin-bottom: 15px;">Type de chambre</h5>
                        @php
                            $roomTypes = \App\Models\RoomType::all();
                        @endphp
                        @foreach($roomTypes as $type)
                            <div style="margin-bottom: 10px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="room_types[]" value="{{ $type->id }}" 
                                           {{ in_array($type->id, (array)request('room_types', [])) ? 'checked' : '' }}>
                                    {{ $type->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Équipements --}}
                    <div class="filter-group" style="margin-bottom: 25px;">
                        <h5 style="margin-bottom: 15px;">Équipements</h5>
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="wifi" value="1" {{ request('wifi') ? 'checked' : '' }}>
                                Wi-Fi
                            </label>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="tv" value="1" {{ request('tv') ? 'checked' : '' }}>
                                Télévision
                            </label>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="air_conditioning" value="1" {{ request('air_conditioning') ? 'checked' : '' }}>
                                Climatisation
                            </label>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="minibar" value="1" {{ request('minibar') ? 'checked' : '' }}>
                                Mini-bar
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%;">Appliquer les filtres</button>
                    <a href="{{ route('booking.search', request()->except(['min_price', 'max_price', 'room_types', 'wifi', 'tv', 'air_conditioning', 'minibar'])) }}" 
                       class="btn-secondary" style="width: 100%; margin-top: 10px; text-align: center;">Réinitialiser</a>
                </form>
            </aside>
            
            {{-- Liste des chambres --}}
            <div>
                @if($availableRooms->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 30px;">
                        @foreach($availableRooms as $room)
                            <div class="room-card-horizontal" style="display: grid; grid-template-columns: 300px 1fr; background: var(--light); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: var(--transition);">
                                <div class="room-image" style="height: 100%; min-height: 250px;">
                                    <img src="{{ $room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                         alt="{{ $room->room_number }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    <span class="room-category">{{ $room->roomType->name }}</span>
                                    
                                    @if($room->isAvailable($checkIn, $checkOut))
                                        <span class="availability-badge" style="position: absolute; bottom: 20px; left: 20px; background: #28a745; color: var(--light); padding: 5px 15px; border-radius: 30px; font-size: 12px;">
                                            <i class="fas fa-check-circle"></i> Disponible
                                        </span>
                                    @else
                                        <span class="availability-badge" style="position: absolute; bottom: 20px; left: 20px; background: #dc3545; color: var(--light); padding: 5px 15px; border-radius: 30px; font-size: 12px;">
                                            <i class="fas fa-times-circle"></i> Non disponible
                                        </span>
                                    @endif
                                </div>
                                
                                <div style="padding: 30px; display: flex; flex-direction: column;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <div>
                                            <h3 style="font-size: 24px; margin-bottom: 5px;">Chambre {{ $room->room_number }}</h3>
                                            <p style="color: var(--text-light);">{{ $room->roomType->description ?? 'Chambre confortable avec toutes les commodités' }}</p>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 28px; font-weight: 700; color: var(--primary);">
                                                {{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA
                                            </div>
                                            <span style="color: var(--text-light); font-size: 14px;">par nuit</span>
                                        </div>
                                    </div>
                                    
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                        <span><i class="fas fa-user-friends" style="color: var(--primary); margin-right: 5px;"></i> {{ $room->max_occupancy }} personnes</span>
                                        <span><i class="fas fa-arrows-alt" style="color: var(--primary); margin-right: 5px;"></i> {{ $room->size ?? 30 }}m²</span>
                                        <span><i class="fas fa-wifi" style="color: var(--primary); margin-right: 5px;"></i> Wi-Fi</span>
                                    </div>
                                    
                                    <div style="display: flex; gap: 20px; margin-bottom: 25px;">
                                        @if($room->has_air_conditioning)
                                            <span style="background: var(--gray); padding: 5px 12px; border-radius: 30px; font-size: 13px;"><i class="fas fa-wind"></i> Climatisation</span>
                                        @endif
                                        @if($room->has_tv)
                                            <span style="background: var(--gray); padding: 5px 12px; border-radius: 30px; font-size: 13px;"><i class="fas fa-tv"></i> TV</span>
                                        @endif
                                        @if($room->has_minibar)
                                            <span style="background: var(--gray); padding: 5px 12px; border-radius: 30px; font-size: 13px;"><i class="fas fa-glass-cheers"></i> Mini-bar</span>
                                        @endif
                                    </div>
                                    
                                    <div style="display: flex; gap: 15px; margin-top: auto;">
                                        <a href="{{ route('room.show', $room->id) }}" class="btn-secondary" style="flex: 1; text-align: center;">
                                            <i class="fas fa-info-circle"></i> Détails
                                        </a>
                                        @if($room->isAvailable($checkIn, $checkOut))
                                            <a href="{{ route('booking.confirm', $room->id) }}" 
                                               class="btn-primary" style="flex: 2; text-align: center;">
                                                <i class="fas fa-calendar-check"></i> Réserver
                                            </a>
                                        @else
                                            <button class="btn-primary" style="flex: 2; opacity: 0.5; cursor: not-allowed;" disabled>
                                                <i class="fas fa-times-circle"></i> Non disponible
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div style="margin-top: 50px;">
                        {{ $availableRooms->appends(request()->query())->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 80px 40px; background: var(--light); border-radius: 15px;">
                        <i class="fas fa-search" style="font-size: 60px; color: var(--primary); opacity: 0.5; margin-bottom: 20px;"></i>
                        <h3 style="font-size: 24px; margin-bottom: 10px;">Aucune chambre disponible</h3>
                        <p style="color: var(--text-light); margin-bottom: 30px;">Essayez de modifier vos dates de séjour ou de réduire le nombre de personnes.</p>
                        <a href="{{ route('home') }}" class="btn-primary">Retour à l'accueil</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function toggleModifySearch() {
        const modifyDiv = document.getElementById('modify-search');
        modifyDiv.style.display = modifyDiv.style.display === 'none' ? 'block' : 'none';
    }
    
    function toggleFilters() {
        const filtersSidebar = document.querySelector('.filters-sidebar');
        if (window.innerWidth <= 768) {
            filtersSidebar.classList.toggle('active');
        }
    }
    
    function applyFilters() {
        const sort = document.getElementById('sort').value;
        document.getElementById('sort-input').value = sort;
        document.getElementById('filter-form').submit();
    }
    
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
    
    // Responsive filters
    window.addEventListener('resize', function() {
        const filtersSidebar = document.querySelector('.filters-sidebar');
        if (window.innerWidth > 768) {
            filtersSidebar.classList.remove('active');
        }
    });
</script>
@endpush

<style>
    @media (max-width: 768px) {
        .search-results > .container > div {
            grid-template-columns: 1fr !important;
        }
        
        .filters-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 80%;
            max-width: 350px;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
            transition: 0.3s;
            border-radius: 0 !important;
        }
        
        .filters-sidebar.active {
            left: 0;
        }
        
        .room-card-horizontal {
            grid-template-columns: 1fr !important;
        }
        
        .room-card-horizontal .room-image {
            min-height: 200px;
        }
    }
</style>
@endsection
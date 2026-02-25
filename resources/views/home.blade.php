@extends('layouts.app')

@section('title', 'SugnuHotel - Luxe et Confort à Dakar')

@push('styles')
<style>
    /* Styles supplémentaires spécifiques à la page d'accueil */
    .swiper-pagination-bullet {
        width: 12px;
        height: 12px;
        background: var(--light);
        opacity: 0.5;
    }
    
    .swiper-pagination-bullet-active {
        opacity: 1;
        background: var(--primary);
    }
    
    .swiper-button-prev,
    .swiper-button-next {
        color: var(--light);
        background: rgba(255,255,255,0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        backdrop-filter: blur(5px);
    }
    
    .swiper-button-prev:after,
    .swiper-button-next:after {
        font-size: 20px;
    }
    
    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        background: var(--primary);
    }
</style>
@endpush

@section('content')
{{-- Hero Section avec Slider --}}
<section class="hero">
    <div class="swiper hero-slider">
        <div class="swiper-wrapper">
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')">
                <div class="hero-content">
                    <span class="hero-subtitle" data-aos="fade-up">Bienvenue à</span>
                    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200">SUGNUHOTEL</h1>
                    <p class="hero-description" data-aos="fade-up" data-aos-delay="400">
                        Découvrez l'art de l'hospitalité sénégalaise dans un cadre d'exception
                    </p>
                    <div class="hero-buttons" data-aos="fade-up" data-aos-delay="600">
                        <a href="#rooms" class="btn-primary">Nos Chambres</a>
                        <a href="#booking" class="btn-secondary">Réserver</a>
                    </div>
                </div>
            </div>
            
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')">
                <div class="hero-content">
                    <span class="hero-subtitle">Luxe et Confort</span>
                    <h1 class="hero-title">Votre Séjour Inoubliable</h1>
                    <p class="hero-description">
                        Des chambres élégantes et des services sur mesure pour un séjour parfait
                    </p>
                    <div class="hero-buttons">
                        <a href="#rooms" class="btn-primary">Découvrir</a>
                        <a href="#booking" class="btn-secondary">Réserver</a>
                    </div>
                </div>
            </div>
            
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')">
                <div class="hero-content">
                    <span class="hero-subtitle">Spa & Bien-être</span>
                    <h1 class="hero-title">Détente Absolue</h1>
                    <p class="hero-description">
                        Offrez-vous un moment de relaxation dans notre spa luxueux
                    </p>
                    <div class="hero-buttons">
                        <a href="#services" class="btn-primary">Nos Services</a>
                        <a href="#booking" class="btn-secondary">Réserver</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>

{{-- Formulaire de réservation rapide --}}
<section class="booking-form" id="booking">
    <div class="container">
        <div class="booking-form-container" data-aos="fade-up">
            <h2>Réservez votre séjour</h2>
            
            <form action="{{ route('booking.search') }}" method="GET" class="booking-form-grid">
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
                    <i class="fas fa-search"></i> Vérifier les disponibilités
                </button>
            </form>
        </div>
    </div>
</section>

{{-- Section Bienvenue --}}
<section class="welcome-section">
    <div class="container">
        <div class="welcome-grid">
            <div class="welcome-content" data-aos="fade-right">
                <span class="section-tag">Bienvenue</span>
                <h2 class="section-title">L'excellence de l'hospitalité sénégalaise</h2>
                <p class="section-description">
                    Situé au cœur de Dakar, SugnuHotel allie le charme traditionnel sénégalais 
                    au luxe contemporain. Découvrez une expérience unique où chaque détail est 
                    pensé pour votre confort et votre bien-être.
                </p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-wifi"></i>
                        <div>
                            <h4>Wi-Fi Haut Débit</h4>
                            <p>Gratuit dans tout l'hôtel</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-utensils"></i>
                        <div>
                            <h4>Restaurant Gastronomique</h4>
                            <p>Cuisine locale et internationale</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-swimming-pool"></i>
                        <div>
                            <h4>Piscine à débordement</h4>
                            <p>Vue sur l'océan</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-spa"></i>
                        <div>
                            <h4>Spa & Bien-être</h4>
                            <p>Massages et soins traditionnels</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="welcome-image" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                     alt="SugnuHotel Luxury">
                <div class="experience-badge">
                    <span class="years">10+</span>
                    <span class="text">Ans d'excellence</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Section Chambres --}}
<section class="rooms-section" id="rooms">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag">Nos Chambres</span>
            <h2 class="section-title">Un choix d'exception</h2>
            <p class="section-description">
                Des chambres élégantes et spacieuses pour tous vos besoins
            </p>
        </div>
        
        <div class="rooms-grid">
            @forelse($rooms ?? [] as $room)
            <div class="room-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="room-image">
                    <img src="{{ $room->images->first()->image_path ?? 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80' }}" 
                         alt="{{ $room->room_number }}">
                    <span class="room-category">{{ $room->roomType->name ?? 'Standard' }}</span>
                </div>
                
                <div class="room-details">
                    <h3 class="room-title">Chambre {{ $room->room_number }}</h3>
                    <p class="room-description">
                        {{ $room->roomType->description ?? 'Chambre confortable avec toutes les commodités' }}
                    </p>
                    
                    <div class="room-features">
                        <span><i class="fas fa-user-friends"></i> {{ $room->max_occupancy }} personnes</span>
                        <span><i class="fas fa-arrows-alt"></i> 30m²</span>
                        <span><i class="fas fa-wifi"></i> Wi-Fi</span>
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
            @empty
                @for($i = 1; $i <= 3; $i++)
                <div class="room-card" data-aos="fade-up">
                    <div class="room-image">
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Chambre Standard">
                        <span class="room-category">Standard</span>
                    </div>
                    
                    <div class="room-details">
                        <h3 class="room-title">Chambre Standard</h3>
                        <p class="room-description">
                            Chambre confortable avec lit double, salle de bain privée et vue sur la ville
                        </p>
                        
                        <div class="room-features">
                            <span><i class="fas fa-user-friends"></i> 2 personnes</span>
                            <span><i class="fas fa-arrows-alt"></i> 25m²</span>
                            <span><i class="fas fa-wifi"></i> Wi-Fi</span>
                        </div>
                        
                        <div class="room-price">
                            <div class="price">
                                75 000 FCFA <span>/nuit</span>
                            </div>
                            <a href="#" class="btn-details">
                                Détails <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endfor
            @endforelse
        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="{{ route('rooms') }}" class="btn-primary">Voir toutes nos chambres</a>
        </div>
    </div>
</section>

{{-- Section Services --}}
<section class="services-section" id="services">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag">Nos Services</span>
            <h2 class="section-title">Des services sur mesure</h2>
            <p class="section-description">
                Pour rendre votre séjour encore plus agréable
            </p>
        </div>
        
        <div class="services-grid">
            <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                <div class="service-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Restaurant Gastronomique</h3>
                <p>Savourez une cuisine raffinée mêlant traditions sénégalaises et saveurs internationales</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                <div class="service-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <h3>Spa & Bien-être</h3>
                <p>Détendez-vous avec nos massages traditionnels et soins du corps exclusifs</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                <div class="service-icon">
                    <i class="fas fa-swimming-pool"></i>
                </div>
                <h3>Piscine à débordement</h3>
                <p>Profitez d'une vue imprenable sur l'océan depuis notre piscine chauffée</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                <div class="service-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h3>Salle de sport</h3>
                <p>Équipements modernes pour garder la forme pendant votre séjour</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="500">
                <div class="service-icon">
                    <i class="fas fa-car"></i>
                </div>
                <h3>Service de navette</h3>
                <p>Transferts aéroport et visites guidées de Dakar</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="600">
                <div class="service-icon">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <h3>Service en chambre 24/7</h3>
                <p>Commandez ce que vous voulez, quand vous voulez</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="700">
                <div class="service-icon">
                    <i class="fas fa-wifi"></i>
                </div>
                <h3>Wi-Fi Haut Débit</h3>
                <p>Connexion internet rapide et gratuite dans tout l'établissement</p>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="800">
                <div class="service-icon">
                    <i class="fas fa-parking"></i>
                </div>
                <h3>Parking sécurisé</h3>
                <p>Parking privé et sécurisé pour nos clients</p>
            </div>
        </div>
    </div>
</section>


{{-- Section Appel à l'action --}}
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title" data-aos="fade-up">Prêt pour une expérience inoubliable ?</h2>
        <p class="cta-description" data-aos="fade-up" data-aos-delay="200">
            Réservez dès maintenant et bénéficiez de nos offres exclusives
        </p>
        <a href="#booking" class="btn-cta" data-aos="fade-up" data-aos-delay="400">
            <i class="fas fa-calendar-check"></i> Réserver maintenant
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Initialisation du slider hero
    const heroSwiper = new Swiper('.hero-slider', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });
    
    // Initialisation du slider témoignages
    const testimonialsSwiper = new Swiper('.testimonials-slider', {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
    
    // Gestion du header au scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Menu mobile
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');
    
    menuToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
    });
    
    // Fermer le menu en cliquant sur un lien
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
    
    // Validation des dates
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    
    checkIn.addEventListener('change', function() {
        const minDate = new Date(this.value);
        minDate.setDate(minDate.getDate() + 1);
        checkOut.min = minDate.toISOString().split('T')[0];
        
        if (checkOut.value < this.value) {
            checkOut.value = minDate.toISOString().split('T')[0];
        }
    });
</script>
@endpush
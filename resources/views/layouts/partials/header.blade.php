@php
    $hideHeader = false;
    $currentRoute = request()->route()->getName();
    
    // Pages où le header doit être caché
    $hiddenRoutes = ['admin.dashboard', 'reception.dashboard', 'login', 'register', 'profile.show', 'profile.edit', 'booking.my-reservations','booking.confirm'];
    
    if (in_array($currentRoute, $hiddenRoutes)) {
        $hideHeader = true;
    }
@endphp

@if(!$hideHeader)

<header class="header">
    
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="logo">
                <span class="logo-text">SUGNU<span class="highlight">HOTEL</span></span>
                <span class="logo-sub">Luxe & Confort</span>
            </a>
            
            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                    <li><a href="#rooms">Chambres & Suites</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                
                <div class="nav-actions">
                    @auth
                        <div class="user-menu">
                            <button class="user-btn">
                                <i class="fas fa-user-circle"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="user-dropdown">
                                <a href="{{ route('booking.my-reservations') }}"><i class="fas fa-calendar-check"></i> Mes réservations</a>
                                <a href="{{ route('profile.show') }}"><i class="fas fa-user"></i> Mon profil</a>
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-cog"></i> Administration</a>
                                @endif
                                @if(Auth::user()->role === 'receptionist')
                                    <a href="{{ route('reception.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-login"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                        <a href="{{ route('register') }}" class="btn-register">S'inscrire</a>
                    @endauth
                    
                    <button class="menu-toggle" id="menuToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</header>
@endif
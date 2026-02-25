{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil - SugnuHotel')

@section('content')
{{-- En-tête --}}
<section class="page-header" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; padding: 150px 0 80px;">
    <div class="container">
        <h1 class="page-title" style="color: var(--light); font-size: 48px; margin-bottom: 20px;">Mon Profil</h1>
        <div class="breadcrumbs" style="color: rgba(255,255,255,0.8);">
            <a href="{{ route('home') }}" style="color: var(--light);">Accueil</a> / 
            <span>Profil</span>
        </div>
    </div>
</section>

{{-- Contenu principal --}}
<section class="profile-section" style="padding: 60px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 30px;">
            {{-- Sidebar --}}
            <aside style="background: var(--light); border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); height: fit-content;">
                {{-- Avatar --}}
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--gray); margin: 0 auto 15px; overflow: hidden; border: 3px solid var(--primary);">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; background: var(--primary); color: var(--light); display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 600;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3 style="font-size: 20px; margin-bottom: 5px;">{{ Auth::user()->name }}</h3>
                    <p style="color: var(--text-light); font-size: 14px;">Membre depuis {{ Auth::user()->created_at->format('F Y') }}</p>
                </div>
                
                {{-- Navigation --}}
                <nav style="display: flex; flex-direction: column; gap: 5px;">
                    <a href="{{ route('profile.show') }}" class="profile-nav-link active" style="padding: 12px 15px; border-radius: 10px; color: var(--text); text-decoration: none; transition: var(--transition); background: var(--primary); color: var(--light);">
                        <i class="fas fa-user" style="width: 20px; margin-right: 10px;"></i> Informations personnelles
                    </a>
                    <a href="{{ route('profile.edit') }}" class="profile-nav-link" style="padding: 12px 15px; border-radius: 10px; color: var(--text); text-decoration: none; transition: var(--transition);">
                        <i class="fas fa-edit" style="width: 20px; margin-right: 10px;"></i> Modifier le profil
                    </a>
                    <a href="{{ route('booking.my-reservations') }}" class="profile-nav-link" style="padding: 12px 15px; border-radius: 10px; color: var(--text); text-decoration: none; transition: var(--transition);">
                        <i class="fas fa-calendar-check" style="width: 20px; margin-right: 10px;"></i> Mes réservations
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 12px 15px; border: none; background: #dc3545; color: var(--light); border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </nav>
            </aside>
            
            {{-- Contenu principal --}}
            <div style="background: var(--light); border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <h2 style="font-size: 28px; margin-bottom: 30px;">Informations personnelles</h2>
                
                {{-- Alertes --}}
                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 30px;">
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- Informations --}}
                <div style="display: grid; gap: 25px;">
                    <div style="display: grid; grid-template-columns: 150px 1fr; padding: 15px; background: var(--gray); border-radius: 10px;">
                        <span style="color: var(--text-light);">Nom complet</span>
                        <span style="font-weight: 500;">{{ Auth::user()->name }}</span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 150px 1fr; padding: 15px; background: var(--gray); border-radius: 10px;">
                        <span style="color: var(--text-light);">Email</span>
                        <span style="font-weight: 500;">{{ Auth::user()->email }}</span>
                    </div>
                    
                    @if(Auth::user()->phone)
                    <div style="display: grid; grid-template-columns: 150px 1fr; padding: 15px; background: var(--gray); border-radius: 10px;">
                        <span style="color: var(--text-light);">Téléphone</span>
                        <span style="font-weight: 500;">{{ Auth::user()->phone }}</span>
                    </div>
                    @endif
                    
                    @if(Auth::user()->address)
                    <div style="display: grid; grid-template-columns: 150px 1fr; padding: 15px; background: var(--gray); border-radius: 10px;">
                        <span style="color: var(--text-light);">Adresse</span>
                        <span style="font-weight: 500;">{{ Auth::user()->address }}</span>
                    </div>
                    @endif
                    
                    <div style="display: grid; grid-template-columns: 150px 1fr; padding: 15px; background: var(--gray); border-radius: 10px;">
                        <span style="color: var(--text-light);">Rôle</span>
                        <span style="font-weight: 500;">
                            @if(Auth::user()->role == 'admin')
                                Administrateur
                            @elseif(Auth::user()->role == 'receptionist')
                                Réceptionniste
                            @else
                                Client
                            @endif
                        </span>
                    </div>
                </div>
                
                <div style="margin-top: 40px; text-align: right;">
                    <a href="{{ route('profile.edit') }}" class="btn-primary">
                        <i class="fas fa-edit"></i> Modifier mes informations
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .profile-nav-link:hover:not(.active) {
        background: var(--gray);
        color: var(--primary);
        padding-left: 20px;
    }
    
    .profile-nav-link.active {
        background: var(--primary);
        color: var(--light);
    }
    
    @media (max-width: 768px) {
        .profile-section > .container > div {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
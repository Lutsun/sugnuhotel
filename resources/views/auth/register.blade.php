@extends('layouts.app')

@section('title', 'Inscription - SugnuHotel')
@section('body-class', 'auth-page')

{{-- Désactiver le header et le footer --}}
@php
    $hideHeader = true;
    $hideFooter = true;
@endphp

@section('content')
<div class="auth-section">
    <div class="auth-container register-container">
        {{-- Colonne gauche avec formulaire --}}
        <div class="auth-form">
            <div class="auth-form-header">
                <a href="{{ route('home') }}" class="auth-logo">
                    <span class="logo-text">SUGNU<span class="highlight">HOTEL</span></span>
                </a>
                <h3>Créer un compte</h3>
                <p>Rejoignez-nous pour profiter d'avantages exclusifs</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form-body">
                @csrf

                {{-- Nom complet --}}
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i>
                        Nom complet
                    </label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                           placeholder="Jean Dupont">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Adresse email
                    </label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="exemple@email.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Téléphone --}}
                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i>
                        Téléphone (optionnel)
                    </label>
                    <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           name="phone" value="{{ old('phone') }}" 
                           placeholder="+221 78 123 45 67">
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mot de passe
                    </label>
                    <div class="password-input-wrapper">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="new-password"
                               placeholder="••••••••">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Confirmation mot de passe --}}
                <div class="form-group">
                    <label for="password-confirm">
                        <i class="fas fa-lock"></i>
                        Confirmer le mot de passe
                    </label>
                    <div class="password-input-wrapper">
                        <input id="password-confirm" type="password" class="form-control" 
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="••••••••">
                        <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Conditions --}}
                <div class="form-group terms-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="terms" id="terms" required>
                        <label for="terms">
                            J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> 
                            et la <a href="#" target="_blank">politique de confidentialité</a>
                        </label>
                    </div>
                </div>

                {{-- Bouton d'inscription --}}
                <button type="submit" class="btn-auth">
                    <span>Créer mon compte</span>
                    <i class="fas fa-user-plus"></i>
                </button>

                {{-- Séparateur --}}
                <div class="auth-divider">
                    <span>ou</span>
                </div>

                {{-- Lien vers connexion --}}
                <div class="auth-redirect">
                    <p>Déjà membre ?</p>
                    <a href="{{ route('login') }}" class="btn-login-link">
                        Se connecter <i class="fas fa-sign-in-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Colonne droite avec image --}}
        <div class="auth-image" style="background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')">
            <div class="auth-image-overlay">
                <div class="auth-image-content">
                    <a href="{{ route('home') }}" class="auth-back-home">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                    <h2>Rejoignez l'expérience SugnuHotel</h2>
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <i class="fas fa-gem"></i>
                            <div>
                                <h4>Avantages membres</h4>
                                <p>Accédez à des offres exclusives et des réductions</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-calendar-check"></i>
                            <div>
                                <h4>Réservations simplifiées</h4>
                                <p>Gérez vos séjours en quelques clics</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <h4>Programme de fidélité</h4>
                                <p>Cumulez des points et gagnez des nuits gratuites</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
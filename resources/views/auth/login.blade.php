@extends('layouts.app')

@section('title', 'Connexion - SugnuHotel')
@section('body-class', 'auth-page')

{{-- Désactiver le header et le footer --}}
@php
    $hideHeader = true;
    $hideFooter = true;
@endphp

@section('content')
<div class="auth-section">
    <div class="auth-container">
        {{-- Colonne gauche avec image --}}
        <div class="auth-image" style="background-image: url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2080&q=80')">
            <div class="auth-image-overlay">
                <div class="auth-image-content">
                    <a href="{{ route('home') }}" class="auth-back-home">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                    <h2>Bienvenue à SugnuHotel</h2>
                    <p>Connectez-vous pour accéder à votre espace personnel et gérer vos réservations</p>
                    <div class="auth-image-features">
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Réservations en ligne</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Offres exclusives</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Service prioritaire</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne droite avec formulaire --}}
        <div class="auth-form">
            <div class="auth-form-header">
                <a href="{{ route('home') }}" class="auth-logo">
                    <span class="logo-text">SUGNU<span class="highlight">HOTEL</span></span>
                </a>
                <h3>Content de vous revoir</h3>
                <p>Connectez-vous à votre compte</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="auth-form-body">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Adresse email
                    </label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           placeholder="exemple@email.com">
                    @error('email')
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
                               name="password" required autocomplete="current-password"
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

                {{-- Options --}}
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Se souvenir de moi</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                {{-- Bouton de connexion --}}
                <button type="submit" class="btn-auth">
                    <span>Se connecter</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                {{-- Séparateur --}}
                <div class="auth-divider">
                    <span>ou</span>
                </div>

                {{-- Lien vers inscription --}}
                <div class="auth-redirect">
                    <p>Pas encore de compte ?</p>
                    <a href="{{ route('register') }}" class="btn-register-link">
                        Créer un compte <i class="fas fa-user-plus"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.currentTarget.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
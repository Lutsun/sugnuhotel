import { Component, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-header',
  imports: [RouterLink, RouterLinkActive],
  template: `
    <div class="hidden sm:block bg-ink-900 text-ink-100 text-xs">
      <div class="max-w-6xl mx-auto px-4 h-9 flex items-center justify-between">
        <div class="flex items-center gap-5">
          <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h1.5a1 1 0 01.98.804l.6 3a1 1 0 01-.27.92l-1.2 1.2a12.04 12.04 0 005.6 5.6l1.2-1.2a1 1 0 01.92-.27l3 .6a1 1 0 01.804.98V17a1 1 0 01-1 1h-1C7.82 18 2 12.18 2 5V4a1 1 0 010-1z"/></svg>
            +221 33 800 00 00
          </span>
          <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M2.94 4.94a1.5 1.5 0 011.06-.44h12a1.5 1.5 0 011.06.44L10 10.5 2.94 4.94zM2 6.4V14.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V6.4l-7.55 5.66a1 1 0 01-1.2 0L2 6.4z"/></svg>
            contact&#64;sugnuhotel.sn
          </span>
        </div>
        <span>Dakar, Sénégal</span>
      </div>
    </div>

    <header class="bg-white/95 backdrop-blur border-b border-ink-100 sticky top-0 z-40">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-16">
        <a routerLink="/" class="flex items-center gap-2 font-display text-2xl font-semibold text-ink-900">
          <span class="w-9 h-9 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-display">SH</span>
          SugnuHotel
        </a>

        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-ink-700">
          <a routerLink="/" routerLinkActive="text-brand-600" [routerLinkActiveOptions]="{ exact: true }" class="hover:text-brand-600 transition">Accueil</a>
          <a routerLink="/rooms" routerLinkActive="text-brand-600" class="hover:text-brand-600 transition">Chambres</a>

          @if (auth.role() === 'client') {
            <a routerLink="/booking/search" routerLinkActive="text-brand-600" class="hover:text-brand-600 transition">Réserver</a>
            <a routerLink="/my-reservations" routerLinkActive="text-brand-600" class="hover:text-brand-600 transition">Mes réservations</a>
          }
          @if (auth.role() === 'admin') {
            <a routerLink="/admin/dashboard" routerLinkActive="text-brand-600" class="hover:text-brand-600 transition">Administration</a>
          }
          @if (auth.role() === 'admin' || auth.role() === 'receptionist') {
            <a routerLink="/reception/dashboard" routerLinkActive="text-brand-600" class="hover:text-brand-600 transition">Réception</a>
          }
        </nav>

        <div class="flex items-center gap-3">
          @if (auth.isAuthenticated()) {
            <div class="relative">
              <button
                type="button"
                class="flex items-center gap-2 text-sm font-medium text-ink-700"
                (click)="menuOpen.set(!menuOpen())"
              >
                <span class="w-8 h-8 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center text-xs font-semibold">
                  {{ initials() }}
                </span>
                <span class="hidden lg:inline">{{ auth.currentUser()?.name }}</span>
                <span class="text-xs text-ink-400">▾</span>
              </button>
              @if (menuOpen()) {
                <div class="absolute right-0 mt-2 w-48 bg-white border border-ink-100 rounded-lg shadow-xl py-1 text-sm overflow-hidden">
                  <a routerLink="/profile" class="block px-4 py-2 hover:bg-brand-50" (click)="menuOpen.set(false)">Mon profil</a>
                  <button type="button" class="w-full text-left px-4 py-2 hover:bg-brand-50" (click)="logout()">Déconnexion</button>
                </div>
              }
            </div>
          } @else {
            <a routerLink="/login" class="hidden sm:inline text-sm font-medium text-ink-700 hover:text-brand-600 transition">Connexion</a>
            <a
              routerLink="/register"
              class="text-sm font-semibold bg-brand-600 text-white px-4 py-2.5 rounded-full hover:bg-brand-700 transition shadow-sm shadow-brand-600/30"
            >
              Réserver
            </a>
          }
          <button type="button" class="md:hidden text-ink-700 p-1" (click)="mobileOpen.set(!mobileOpen())" aria-label="Menu">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
        </div>
      </div>

      @if (mobileOpen()) {
        <nav class="md:hidden border-t border-ink-100 px-4 py-3 flex flex-col gap-3 text-sm font-medium text-ink-700 bg-white">
          <a routerLink="/" (click)="mobileOpen.set(false)">Accueil</a>
          <a routerLink="/rooms" (click)="mobileOpen.set(false)">Chambres</a>
          @if (auth.role() === 'client') {
            <a routerLink="/booking/search" (click)="mobileOpen.set(false)">Réserver</a>
            <a routerLink="/my-reservations" (click)="mobileOpen.set(false)">Mes réservations</a>
          }
          @if (auth.role() === 'admin') {
            <a routerLink="/admin/dashboard" (click)="mobileOpen.set(false)">Administration</a>
          }
          @if (auth.role() === 'admin' || auth.role() === 'receptionist') {
            <a routerLink="/reception/dashboard" (click)="mobileOpen.set(false)">Réception</a>
          }
          @if (!auth.isAuthenticated()) {
            <a routerLink="/login" (click)="mobileOpen.set(false)">Connexion</a>
          }
        </nav>
      }
    </header>
  `,
})
export class Header {
  protected readonly auth = inject(AuthService);
  protected readonly menuOpen = signal(false);
  protected readonly mobileOpen = signal(false);

  protected initials(): string {
    const name = this.auth.currentUser()?.name ?? '';
    return name
      .split(' ')
      .map((part) => part[0])
      .slice(0, 2)
      .join('')
      .toUpperCase();
  }

  protected logout(): void {
    this.menuOpen.set(false);
    this.auth.logout();
  }
}

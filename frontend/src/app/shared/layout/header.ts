import { Component, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-header',
  imports: [RouterLink, RouterLinkActive],
  template: `
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-16">
        <a routerLink="/" class="text-xl font-bold text-brand-700">SugnuHotel</a>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700">
          <a routerLink="/" routerLinkActive="text-brand-700" [routerLinkActiveOptions]="{ exact: true }">Accueil</a>
          <a routerLink="/rooms" routerLinkActive="text-brand-700">Chambres</a>

          @if (auth.role() === 'client') {
            <a routerLink="/booking/search" routerLinkActive="text-brand-700">Réserver</a>
            <a routerLink="/my-reservations" routerLinkActive="text-brand-700">Mes réservations</a>
          }
          @if (auth.role() === 'admin') {
            <a routerLink="/admin/dashboard" routerLinkActive="text-brand-700">Administration</a>
          }
          @if (auth.role() === 'admin' || auth.role() === 'receptionist') {
            <a routerLink="/reception/dashboard" routerLinkActive="text-brand-700">Réception</a>
          }
        </nav>

        <div class="flex items-center gap-3">
          @if (auth.isAuthenticated()) {
            <div class="relative">
              <button
                type="button"
                class="flex items-center gap-2 text-sm font-medium text-slate-700"
                (click)="menuOpen.set(!menuOpen())"
              >
                {{ auth.currentUser()?.name }}
                <span class="text-xs">▾</span>
              </button>
              @if (menuOpen()) {
                <div class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-md shadow-lg py-1 text-sm">
                  <a routerLink="/profile" class="block px-4 py-2 hover:bg-slate-50" (click)="menuOpen.set(false)">Mon profil</a>
                  <button type="button" class="w-full text-left px-4 py-2 hover:bg-slate-50" (click)="logout()">Déconnexion</button>
                </div>
              }
            </div>
          } @else {
            <a routerLink="/login" class="text-sm font-medium text-slate-700 hover:text-brand-700">Connexion</a>
            <a routerLink="/register" class="text-sm font-medium bg-brand-700 text-white px-4 py-2 rounded-md hover:bg-brand-800">
              Inscription
            </a>
          }
        </div>
      </div>
    </header>
  `,
})
export class Header {
  protected readonly auth = inject(AuthService);
  protected readonly menuOpen = signal(false);

  protected logout(): void {
    this.menuOpen.set(false);
    this.auth.logout();
  }
}

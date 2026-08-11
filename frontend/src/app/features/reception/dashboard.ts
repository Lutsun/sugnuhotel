import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ReceptionDashboard, ReceptionDashboardService } from '../../core/services/reception/dashboard.service';
import { Reservation } from '../../core/models/reservation.model';
import { StatCard } from '../../shared/ui/stat-card';

@Component({
  selector: 'app-reception-dashboard',
  imports: [RouterLink, StatCard],
  template: `
    <div class="bg-ink-50/60 py-10 mb-2">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between flex-wrap gap-4">
        <div>
          <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Réception</span>
          <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2">Tableau de bord</h1>
        </div>
        <div class="flex gap-2">
          <a routerLink="/reception/calendar" class="bg-white border border-ink-100 rounded-full px-4 py-2 text-sm font-medium hover:shadow-md transition flex items-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" /><path stroke-linecap="round" d="M3 10h18M8 2v4M16 2v4" /></svg>
            Calendrier
          </a>
          <a routerLink="/reception/reservations" class="bg-brand-600 text-white rounded-full px-4 py-2 text-sm font-medium hover:bg-brand-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" /></svg>
            Réservations
          </a>
        </div>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 pb-16">
      @if (data(); as data) {
        <div class="grid sm:grid-cols-3 gap-4 mb-8">
          <app-stat-card
            label="Chambres disponibles"
            [value]="data.rooms.available + ' / ' + data.rooms.total"
            iconClass="bg-brand-50 text-brand-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18h18M6 10V7a2 2 0 012-2h1a2 2 0 012 2v3" />
            </svg>
          </app-stat-card>
          <app-stat-card
            label="Arrivées aujourd'hui"
            [value]="data.today_arrivals.length"
            iconClass="bg-amber-50 text-amber-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l5-5m-5 5l5 5m-5-5h13M16 5h5v14h-5" />
            </svg>
          </app-stat-card>
          <app-stat-card
            label="Départs aujourd'hui"
            [value]="data.today_departures.length"
            iconClass="bg-slate-100 text-slate-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12l-5-5m5 5l-5 5m5-5H8M8 5H3v14h5" />
            </svg>
          </app-stat-card>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
          <div class="bg-white border border-ink-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-ink-100 flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l5-5m-5 5l5 5m-5-5h13M16 5h5v14h-5" /></svg>
              <h2 class="font-display font-semibold text-ink-900">Arrivées du jour</h2>
            </div>
            <ul class="text-sm divide-y divide-ink-100">
              @for (r of data.today_arrivals; track r.id) {
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                  <span class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 shrink-0 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center text-xs font-semibold">{{ initials(r) }}</span>
                    <span class="truncate">{{ r.user?.name }} — Ch. {{ r.room?.room_number }}</span>
                  </span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline text-xs font-semibold shrink-0">Voir</a>
                </li>
              } @empty {
                <li class="px-5 py-6 text-ink-400 text-center">Aucune arrivée aujourd'hui.</li>
              }
            </ul>
          </div>

          <div class="bg-white border border-ink-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-ink-100 flex items-center gap-2">
              <svg class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12l-5-5m5 5l-5 5m5-5H8M8 5H3v14h5" /></svg>
              <h2 class="font-display font-semibold text-ink-900">Départs du jour</h2>
            </div>
            <ul class="text-sm divide-y divide-ink-100">
              @for (r of data.today_departures; track r.id) {
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                  <span class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 shrink-0 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-semibold">{{ initials(r) }}</span>
                    <span class="truncate">{{ r.user?.name }} — Ch. {{ r.room?.room_number }}</span>
                  </span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline text-xs font-semibold shrink-0">Voir</a>
                </li>
              } @empty {
                <li class="px-5 py-6 text-ink-400 text-center">Aucun départ aujourd'hui.</li>
              }
            </ul>
          </div>

          <div class="bg-white border border-ink-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-ink-100 flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 10-3-6.65" /></svg>
              <h2 class="font-display font-semibold text-ink-900">Clients actuellement présents</h2>
            </div>
            <ul class="text-sm divide-y divide-ink-100">
              @for (r of data.current_guests; track r.id) {
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                  <span class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-semibold">{{ initials(r) }}</span>
                    <span class="truncate">{{ r.user?.name }} — Ch. {{ r.room?.room_number }}</span>
                  </span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline text-xs font-semibold shrink-0">Voir</a>
                </li>
              } @empty {
                <li class="px-5 py-6 text-ink-400 text-center">Aucun client présent.</li>
              }
            </ul>
          </div>

          <div class="bg-white border border-ink-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-ink-100 flex items-center gap-2">
              <svg class="w-4 h-4 text-violet-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" /><path stroke-linecap="round" d="M3 10h18M8 2v4M16 2v4" /></svg>
              <h2 class="font-display font-semibold text-ink-900">Arrivées à venir (7 jours)</h2>
            </div>
            <ul class="text-sm divide-y divide-ink-100">
              @for (r of data.upcoming_arrivals; track r.id) {
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                  <span class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 shrink-0 rounded-full bg-violet-50 text-violet-700 flex items-center justify-center text-xs font-semibold">{{ initials(r) }}</span>
                    <span class="truncate">{{ r.user?.name }} — {{ r.check_in_date }}</span>
                  </span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline text-xs font-semibold shrink-0">Voir</a>
                </li>
              } @empty {
                <li class="px-5 py-6 text-ink-400 text-center">Aucune arrivée prévue.</li>
              }
            </ul>
          </div>
        </div>
      }
    </div>
  `,
})
export class ReceptionDashboardPage implements OnInit {
  private readonly service = inject(ReceptionDashboardService);
  protected readonly data = signal<ReceptionDashboard | null>(null);

  ngOnInit(): void {
    this.service.get().subscribe((res) => this.data.set(res));
  }

  protected initials(r: Reservation): string {
    const name = r.user?.name ?? '';
    return name
      .split(' ')
      .map((part) => part[0])
      .slice(0, 2)
      .join('')
      .toUpperCase();
  }
}

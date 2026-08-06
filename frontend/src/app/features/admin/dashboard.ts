import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AdminDashboard, AdminDashboardService } from '../../core/services/admin/dashboard.service';
import { StatCard } from '../../shared/ui/stat-card';
import { StatusBadge } from '../../shared/ui/status-badge';

@Component({
  selector: 'app-admin-dashboard',
  imports: [DecimalPipe, RouterLink, StatCard, StatusBadge],
  template: `
    <div class="bg-ink-50/60 py-10 mb-2">
      <div class="max-w-6xl mx-auto px-4">
        <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Administration</span>
        <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2">Tableau de bord</h1>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 pb-16">
      @if (data(); as data) {
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <app-stat-card
            label="Chambres"
            [value]="data.rooms.total"
            [sublabel]="data.rooms.available + ' disponibles · ' + data.rooms.occupied + ' occupées'"
            iconClass="bg-brand-50 text-brand-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18h18M6 10V7a2 2 0 012-2h1a2 2 0 012 2v3" />
            </svg>
          </app-stat-card>

          <app-stat-card
            label="Taux d'occupation"
            [value]="data.rooms.occupancy_rate + '%'"
            iconClass="bg-blue-50 text-blue-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l4-5 3 3 5-7" />
            </svg>
          </app-stat-card>

          <app-stat-card
            label="Réservations"
            [value]="data.reservations.total"
            [sublabel]="data.reservations.pending + ' en attente · ' + data.reservations.confirmed + ' confirmées'"
            iconClass="bg-violet-50 text-violet-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
            </svg>
          </app-stat-card>

          <app-stat-card
            label="Revenu du mois"
            [value]="(data.revenue.monthly | number) + ' FCFA'"
            [sublabel]="'Total : ' + (data.revenue.total | number) + ' FCFA'"
            iconClass="bg-green-50 text-green-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 2c1.66 0 3 .9 3 2m-3 6v2m0-2c-1.66 0-3-.9-3-2" />
              <circle cx="12" cy="12" r="9" />
            </svg>
          </app-stat-card>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 mb-8">
          <app-stat-card
            label="Arrivées aujourd'hui"
            [value]="data.today.arrivals"
            iconClass="bg-amber-50 text-amber-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l5-5m-5 5l5 5m-5-5h13M16 5h5v14h-5" />
            </svg>
          </app-stat-card>
          <app-stat-card
            label="Départs aujourd'hui"
            [value]="data.today.departures"
            iconClass="bg-slate-100 text-slate-700"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12l-5-5m5 5l-5 5m5-5H8M8 5H3v14h5" />
            </svg>
          </app-stat-card>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-10">
          <a routerLink="/admin/room-types" class="bg-white border border-ink-100 rounded-xl p-4 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="w-9 h-9 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
            </div>
            <span class="text-sm font-medium text-ink-900">Types de chambres</span>
          </a>
          <a routerLink="/admin/rooms" class="bg-white border border-ink-100 rounded-xl p-4 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18h18M6 10V7a2 2 0 012-2h1a2 2 0 012 2v3" /></svg>
            </div>
            <span class="text-sm font-medium text-ink-900">Chambres</span>
          </a>
          <a routerLink="/admin/services" class="bg-white border border-ink-100 rounded-xl p-4 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="w-9 h-9 rounded-full bg-violet-50 text-violet-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.9 4.6 4.8.7-3.5 3.4.8 4.8-4-2.1-4 2.1.8-4.8-3.5-3.4 4.8-.7z" /></svg>
            </div>
            <span class="text-sm font-medium text-ink-900">Services</span>
          </a>
          <a routerLink="/admin/users" class="bg-white border border-ink-100 rounded-xl p-4 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="w-9 h-9 rounded-full bg-green-50 text-green-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 10-3-6.65" /></svg>
            </div>
            <span class="text-sm font-medium text-ink-900">Utilisateurs</span>
          </a>
        </div>

        <div class="bg-white border border-ink-100 rounded-xl overflow-hidden">
          <div class="px-5 py-4 border-b border-ink-100">
            <h2 class="font-display font-semibold text-ink-900">Dernières réservations</h2>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-ink-50/60 text-left text-ink-400">
              <tr>
                <th class="px-5 py-2.5 font-medium">N°</th>
                <th class="px-5 py-2.5 font-medium">Client</th>
                <th class="px-5 py-2.5 font-medium">Chambre</th>
                <th class="px-5 py-2.5 font-medium">Statut</th>
                <th class="px-5 py-2.5 font-medium">Total</th>
              </tr>
            </thead>
            <tbody>
              @for (r of data.recent_reservations; track r.id) {
                <tr class="border-t border-ink-100 hover:bg-ink-50/40">
                  <td class="px-5 py-3 font-medium text-ink-900">{{ r.reservation_number }}</td>
                  <td class="px-5 py-3">{{ r.user?.name }}</td>
                  <td class="px-5 py-3">{{ r.room?.room_number }}</td>
                  <td class="px-5 py-3"><app-status-badge [status]="r.status" /></td>
                  <td class="px-5 py-3 font-medium text-ink-900">{{ r.total_price | number }} FCFA</td>
                </tr>
              } @empty {
                <tr><td colspan="5" class="px-5 py-8 text-center text-ink-400">Aucune réservation pour le moment.</td></tr>
              }
            </tbody>
          </table>
        </div>
      }
    </div>
  `,
})
export class AdminDashboardPage implements OnInit {
  private readonly service = inject(AdminDashboardService);
  protected readonly data = signal<AdminDashboard | null>(null);

  ngOnInit(): void {
    this.service.get().subscribe((res) => this.data.set(res));
  }
}

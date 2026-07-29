import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ReceptionDashboard, ReceptionDashboardService } from '../../core/services/reception/dashboard.service';

@Component({
  selector: 'app-reception-dashboard',
  imports: [RouterLink],
  template: `
    <div class="max-w-6xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Tableau de bord réception</h1>
        <div class="flex gap-2">
          <a routerLink="/reception/calendar" class="bg-white border border-slate-200 rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-50">Calendrier</a>
          <a routerLink="/reception/reservations" class="bg-white border border-slate-200 rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-50">Réservations</a>
        </div>
      </div>

      @if (data(); as data) {
        <div class="grid sm:grid-cols-3 gap-4 mb-8">
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Chambres disponibles</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.rooms.available }} / {{ data.rooms.total }}</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Arrivées aujourd'hui</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.today_arrivals.length }}</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Départs aujourd'hui</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.today_departures.length }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h2 class="font-semibold text-slate-900 mb-3">Arrivées du jour</h2>
            <ul class="text-sm divide-y divide-slate-100">
              @for (r of data.today_arrivals; track r.id) {
                <li class="py-2 flex justify-between">
                  <span>{{ r.user?.name }} — Ch. {{ r.room?.room_number }}</span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline">Voir</a>
                </li>
              } @empty {
                <li class="py-2 text-slate-400">Aucune arrivée aujourd'hui.</li>
              }
            </ul>
          </div>

          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h2 class="font-semibold text-slate-900 mb-3">Départs du jour</h2>
            <ul class="text-sm divide-y divide-slate-100">
              @for (r of data.today_departures; track r.id) {
                <li class="py-2 flex justify-between">
                  <span>{{ r.user?.name }} — Ch. {{ r.room?.room_number }}</span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline">Voir</a>
                </li>
              } @empty {
                <li class="py-2 text-slate-400">Aucun départ aujourd'hui.</li>
              }
            </ul>
          </div>

          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h2 class="font-semibold text-slate-900 mb-3">Clients actuellement présents</h2>
            <ul class="text-sm divide-y divide-slate-100">
              @for (r of data.current_guests; track r.id) {
                <li class="py-2 flex justify-between">
                  <span>{{ r.user?.name }} — Ch. {{ r.room?.room_number }}</span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline">Voir</a>
                </li>
              } @empty {
                <li class="py-2 text-slate-400">Aucun client présent.</li>
              }
            </ul>
          </div>

          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h2 class="font-semibold text-slate-900 mb-3">Arrivées à venir (7 jours)</h2>
            <ul class="text-sm divide-y divide-slate-100">
              @for (r of data.upcoming_arrivals; track r.id) {
                <li class="py-2 flex justify-between">
                  <span>{{ r.user?.name }} — {{ r.check_in_date }}</span>
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline">Voir</a>
                </li>
              } @empty {
                <li class="py-2 text-slate-400">Aucune arrivée prévue.</li>
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
}

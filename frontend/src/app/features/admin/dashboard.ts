import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AdminDashboard, AdminDashboardService } from '../../core/services/admin/dashboard.service';

@Component({
  selector: 'app-admin-dashboard',
  imports: [DecimalPipe, RouterLink],
  template: `
    <div class="max-w-6xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Tableau de bord</h1>

      @if (data(); as data) {
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Chambres</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.rooms.total }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ data.rooms.available }} disponibles · {{ data.rooms.occupied }} occupées</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Taux d'occupation</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.rooms.occupancy_rate }}%</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Réservations</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.reservations.total }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ data.reservations.pending }} en attente · {{ data.reservations.confirmed }} confirmées</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Revenu du mois</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.revenue.monthly | number }} FCFA</p>
            <p class="text-xs text-slate-500 mt-1">Total : {{ data.revenue.total | number }} FCFA</p>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 mb-8">
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Arrivées aujourd'hui</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.today.arrivals }}</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-xs text-slate-500">Départs aujourd'hui</p>
            <p class="text-2xl font-bold text-slate-900">{{ data.today.departures }}</p>
          </div>
        </div>

        <div class="flex gap-3 mb-8 flex-wrap">
          <a routerLink="/admin/room-types" class="bg-white border border-slate-200 rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-50">Types de chambres</a>
          <a routerLink="/admin/rooms" class="bg-white border border-slate-200 rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-50">Chambres</a>
          <a routerLink="/admin/services" class="bg-white border border-slate-200 rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-50">Services</a>
          <a routerLink="/admin/users" class="bg-white border border-slate-200 rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-50">Utilisateurs</a>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-2">N°</th>
                <th class="px-4 py-2">Client</th>
                <th class="px-4 py-2">Chambre</th>
                <th class="px-4 py-2">Statut</th>
                <th class="px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody>
              @for (r of data.recent_reservations; track r.id) {
                <tr class="border-t border-slate-100">
                  <td class="px-4 py-2">{{ r.reservation_number }}</td>
                  <td class="px-4 py-2">{{ r.user?.name }}</td>
                  <td class="px-4 py-2">{{ r.room?.room_number }}</td>
                  <td class="px-4 py-2">{{ r.status }}</td>
                  <td class="px-4 py-2">{{ r.total_price | number }} FCFA</td>
                </tr>
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

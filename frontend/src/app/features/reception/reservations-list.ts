import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ReceptionReservationService } from '../../core/services/reception/reservation.service';
import { Reservation } from '../../core/models/reservation.model';
import { Pagination } from '../../shared/ui/pagination';

@Component({
  selector: 'app-reception-reservations-list',
  imports: [RouterLink, FormsModule, DecimalPipe, Pagination],
  template: `
    <div class="max-w-6xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Réservations</h1>
        <a routerLink="/reception/reservations/new" class="bg-brand-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-brand-800">
          + Nouvelle réservation
        </a>
      </div>

      <div class="flex gap-3 mb-4">
        <input
          type="text"
          placeholder="Rechercher (n°, client)"
          class="border rounded-md px-3 py-2 text-sm flex-1"
          [(ngModel)]="search"
          (ngModelChange)="load(1)"
        />
        <select class="border rounded-md px-3 py-2 text-sm" [(ngModel)]="status" (ngModelChange)="load(1)">
          <option value="">Tous les statuts</option>
          <option value="pending">En attente</option>
          <option value="confirmed">Confirmée</option>
          <option value="checked_in">Enregistrée</option>
          <option value="checked_out">Terminée</option>
          <option value="cancelled">Annulée</option>
        </select>
      </div>

      <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-2">N°</th>
              <th class="px-4 py-2">Client</th>
              <th class="px-4 py-2">Chambre</th>
              <th class="px-4 py-2">Dates</th>
              <th class="px-4 py-2">Statut</th>
              <th class="px-4 py-2">Total</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @for (r of reservations(); track r.id) {
              <tr class="border-t border-slate-100">
                <td class="px-4 py-2">{{ r.reservation_number }}</td>
                <td class="px-4 py-2">{{ r.user?.name }}</td>
                <td class="px-4 py-2">{{ r.room?.room_number }}</td>
                <td class="px-4 py-2">{{ r.check_in_date }} → {{ r.check_out_date }}</td>
                <td class="px-4 py-2">{{ r.status }}</td>
                <td class="px-4 py-2">{{ r.total_price | number }} FCFA</td>
                <td class="px-4 py-2 text-right">
                  <a [routerLink]="['/reception/reservations', r.id]" class="text-brand-700 hover:underline">Voir</a>
                </td>
              </tr>
            } @empty {
              <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Aucune réservation trouvée.</td></tr>
            }
          </tbody>
        </table>
      </div>

      <app-pagination [currentPage]="currentPage()" [lastPage]="lastPage()" (pageChange)="load($event)" />
    </div>
  `,
})
export class ReceptionReservationsList implements OnInit {
  private readonly service = inject(ReceptionReservationService);

  protected readonly reservations = signal<Reservation[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);
  protected search = '';
  protected status = '';

  ngOnInit(): void {
    this.load(1);
  }

  protected load(page: number): void {
    this.service.list({ status: this.status || undefined, search: this.search || undefined, page }).subscribe((res) => {
      this.reservations.set(res.data);
      this.currentPage.set(res.meta.current_page);
      this.lastPage.set(res.meta.last_page);
    });
  }
}

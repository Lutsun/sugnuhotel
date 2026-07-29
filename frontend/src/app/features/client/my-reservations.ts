import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { BookingService } from '../../core/services/booking.service';
import { Reservation } from '../../core/models/reservation.model';
import { Pagination } from '../../shared/ui/pagination';

@Component({
  selector: 'app-my-reservations',
  imports: [RouterLink, DecimalPipe, Pagination],
  template: `
    <div class="max-w-4xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Mes réservations</h1>

      <div class="space-y-4">
        @for (reservation of reservations(); track reservation.id) {
          <a
            [routerLink]="['/my-reservations', reservation.id]"
            class="block bg-white border border-slate-200 rounded-lg p-4 hover:shadow-md transition"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="font-semibold text-slate-900">{{ reservation.reservation_number }}</p>
                <p class="text-sm text-slate-500">
                  Chambre {{ reservation.room?.room_number }} — {{ reservation.check_in_date }} au {{ reservation.check_out_date }}
                </p>
              </div>
              <div class="text-right">
                <span
                  class="text-xs font-semibold px-2 py-1 rounded-full"
                  [class.bg-green-100]="reservation.status === 'confirmed'"
                  [class.text-green-800]="reservation.status === 'confirmed'"
                  [class.bg-blue-100]="reservation.status === 'checked_in'"
                  [class.text-blue-800]="reservation.status === 'checked_in'"
                  [class.bg-slate-100]="reservation.status === 'checked_out'"
                  [class.text-slate-800]="reservation.status === 'checked_out'"
                  [class.bg-red-100]="reservation.status === 'cancelled'"
                  [class.text-red-800]="reservation.status === 'cancelled'"
                  [class.bg-amber-100]="reservation.status === 'pending'"
                  [class.text-amber-800]="reservation.status === 'pending'"
                >
                  {{ reservation.status }}
                </span>
                <p class="text-brand-700 font-bold mt-1">{{ reservation.total_price | number }} FCFA</p>
              </div>
            </div>
          </a>
        } @empty {
          <p class="text-slate-500 text-center py-12">Vous n'avez aucune réservation pour le moment.</p>
        }
      </div>

      <app-pagination [currentPage]="currentPage()" [lastPage]="lastPage()" (pageChange)="load($event)" />
    </div>
  `,
})
export class MyReservations implements OnInit {
  private readonly bookingService = inject(BookingService);

  protected readonly reservations = signal<Reservation[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);

  ngOnInit(): void {
    this.load(1);
  }

  protected load(page: number): void {
    this.bookingService.myReservations(page).subscribe((res) => {
      this.reservations.set(res.data);
      this.currentPage.set(res.meta.current_page);
      this.lastPage.set(res.meta.last_page);
    });
  }
}

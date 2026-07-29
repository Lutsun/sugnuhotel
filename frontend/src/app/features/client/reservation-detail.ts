import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { BookingService } from '../../core/services/booking.service';
import { Reservation } from '../../core/models/reservation.model';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-reservation-detail',
  imports: [RouterLink, DecimalPipe],
  template: `
    @if (reservation(); as reservation) {
      <div class="max-w-3xl mx-auto px-4 py-10">
        <a routerLink="/my-reservations" class="text-sm text-brand-700 hover:underline">← Retour à mes réservations</a>

        <div class="bg-white border border-slate-200 rounded-lg p-6 mt-4 space-y-4">
          <div class="flex justify-between items-start">
            <h1 class="text-xl font-bold text-slate-900">{{ reservation.reservation_number }}</h1>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-800">{{ reservation.status }}</span>
          </div>

          <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <p><span class="text-slate-500">Chambre :</span> {{ reservation.room?.room_number }} — {{ reservation.room?.room_type?.name }}</p>
            <p><span class="text-slate-500">Arrivée :</span> {{ reservation.check_in_date }}</p>
            <p><span class="text-slate-500">Départ :</span> {{ reservation.check_out_date }}</p>
            <p><span class="text-slate-500">Voyageurs :</span> {{ reservation.number_of_adults }} adulte(s), {{ reservation.number_of_children }} enfant(s)</p>
          </div>

          @if (reservation.services.length) {
            <div>
              <h2 class="font-semibold text-slate-900 mb-2">Services</h2>
              <ul class="text-sm text-slate-600 space-y-1">
                @for (item of reservation.services; track item.id) {
                  <li>{{ item.service?.name }} × {{ item.quantity }} — {{ item.price | number }} FCFA</li>
                }
              </ul>
            </div>
          }

          @if (reservation.special_requests) {
            <p class="text-sm"><span class="text-slate-500">Demandes :</span> {{ reservation.special_requests }}</p>
          }

          <p class="text-lg font-bold text-brand-700">Total : {{ reservation.total_price | number }} FCFA</p>

          @if (reservation.status === 'pending' || reservation.status === 'confirmed') {
            <button
              type="button"
              class="bg-red-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-red-700 disabled:opacity-50"
              [disabled]="cancelling()"
              (click)="cancel()"
            >
              Annuler la réservation
            </button>
          }
        </div>
      </div>
    }
  `,
})
export class ReservationDetail implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly bookingService = inject(BookingService);
  private readonly toast = inject(ToastService);

  protected readonly reservation = signal<Reservation | null>(null);
  protected readonly cancelling = signal(false);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.bookingService.show(id).subscribe((res) => this.reservation.set(res.data));
  }

  protected cancel(): void {
    const reservation = this.reservation();
    if (!reservation) return;
    this.cancelling.set(true);

    this.bookingService.cancel(reservation.id).subscribe({
      next: (res) => {
        this.cancelling.set(false);
        this.reservation.set(res.data);
        this.toast.success('Réservation annulée.');
      },
      error: (err) => {
        this.cancelling.set(false);
        this.toast.error(err.error?.message ?? "Impossible d'annuler cette réservation.");
      },
    });
  }
}

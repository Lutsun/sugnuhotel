import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { BookingService, ConfirmDetails } from '../../core/services/booking.service';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-booking-confirm',
  imports: [FormsModule, DecimalPipe],
  template: `
    @if (details(); as details) {
      <div class="max-w-3xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">Confirmer votre réservation</h1>

        <div class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
          <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <p><span class="text-slate-500">Chambre :</span> {{ details.room.room_number }} — {{ details.room.room_type?.name }}</p>
            <p><span class="text-slate-500">Nuits :</span> {{ details.nights }}</p>
            <p><span class="text-slate-500">Arrivée :</span> {{ details.check_in }}</p>
            <p><span class="text-slate-500">Départ :</span> {{ details.check_out }}</p>
            <p><span class="text-slate-500">Voyageurs :</span> {{ details.adults }} adulte(s), {{ details.children }} enfant(s)</p>
            <p><span class="text-slate-500">Prix chambre :</span> {{ details.room_price | number }} FCFA</p>
          </div>

          @if (details.services.length) {
            <div>
              <h2 class="font-semibold text-slate-900 mb-2">Services additionnels</h2>
              <div class="space-y-2">
                @for (service of details.services; track service.id) {
                  <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" [checked]="selectedServices.has(service.id)" (change)="toggleService(service.id)" />
                    {{ service.name }} — {{ service.price | number }} FCFA
                  </label>
                }
              </div>
            </div>
          }

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Demandes particulières</label>
            <textarea [(ngModel)]="specialRequests" rows="3" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
          </div>

          <p class="text-lg font-bold text-brand-700">Total estimé : {{ total() | number }} FCFA</p>

          <button
            type="button"
            class="w-full bg-brand-700 text-white font-semibold py-3 rounded-md hover:bg-brand-800 disabled:opacity-50"
            [disabled]="submitting()"
            (click)="submit()"
          >
            Confirmer la réservation
          </button>
        </div>
      </div>
    }
  `,
})
export class BookingConfirm implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly bookingService = inject(BookingService);
  private readonly toast = inject(ToastService);

  protected readonly details = signal<ConfirmDetails | null>(null);
  protected readonly submitting = signal(false);
  protected readonly selectedServices = new Set<number>();
  protected specialRequests = '';

  private roomId!: number;

  ngOnInit(): void {
    this.roomId = Number(this.route.snapshot.paramMap.get('room'));
    const query = this.route.snapshot.queryParamMap;
    const checkIn = query.get('check_in')!;
    const checkOut = query.get('check_out')!;
    const adults = Number(query.get('adults') ?? 1);
    const children = Number(query.get('children') ?? 0);

    this.bookingService.confirmDetails(this.roomId, checkIn, checkOut, adults, children).subscribe((res) => this.details.set(res));
  }

  protected toggleService(id: number): void {
    if (this.selectedServices.has(id)) {
      this.selectedServices.delete(id);
    } else {
      this.selectedServices.add(id);
    }
  }

  protected total(): number {
    const details = this.details();
    if (!details) return 0;
    const servicesTotal = details.services
      .filter((s) => this.selectedServices.has(s.id))
      .reduce((sum, s) => sum + s.price, 0);
    return details.room_price + servicesTotal;
  }

  protected submit(): void {
    const details = this.details();
    if (!details) return;
    this.submitting.set(true);

    this.bookingService
      .create({
        room_id: this.roomId,
        check_in: details.check_in,
        check_out: details.check_out,
        adults: details.adults,
        children: details.children,
        special_requests: this.specialRequests || undefined,
        services: Array.from(this.selectedServices),
      })
      .subscribe({
        next: (res) => {
          this.toast.success(`Réservation ${res.data.reservation_number} confirmée !`);
          this.router.navigate(['/my-reservations', res.data.id]);
        },
        error: (err) => {
          this.submitting.set(false);
          this.toast.error(err.error?.message ?? 'Impossible de confirmer la réservation.');
        },
      });
  }
}

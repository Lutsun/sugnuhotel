import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ReceptionReservationService } from '../../core/services/reception/reservation.service';
import { Reservation } from '../../core/models/reservation.model';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-reception-reservation-detail',
  imports: [RouterLink, ReactiveFormsModule, DecimalPipe],
  template: `
    @if (reservation(); as reservation) {
      <div class="max-w-3xl mx-auto px-4 py-10">
        <a routerLink="/reception/reservations" class="text-sm text-brand-700 hover:underline">← Retour aux réservations</a>

        <div class="bg-white border border-slate-200 rounded-lg p-6 mt-4 space-y-4">
          <div class="flex justify-between items-start">
            <h1 class="text-xl font-bold text-slate-900">{{ reservation.reservation_number }}</h1>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-800">{{ reservation.status }}</span>
          </div>

          <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <p><span class="text-slate-500">Client :</span> {{ reservation.user?.name }} ({{ reservation.user?.email }})</p>
            <p><span class="text-slate-500">Chambre :</span> {{ reservation.room?.room_number }} — {{ reservation.room?.room_type?.name }}</p>
            <p><span class="text-slate-500">Voyageurs :</span> {{ reservation.number_of_adults }} adulte(s), {{ reservation.number_of_children }} enfant(s)</p>
            <p><span class="text-slate-500">Total :</span> {{ reservation.total_price | number }} FCFA</p>
          </div>

          <div class="flex gap-2 flex-wrap">
            @if (reservation.status === 'confirmed') {
              <button type="button" class="bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-md hover:bg-blue-700" (click)="checkIn()">
                Check-in
              </button>
            }
            @if (reservation.status === 'checked_in') {
              <button type="button" class="bg-slate-700 text-white text-sm font-semibold px-4 py-2 rounded-md hover:bg-slate-800" (click)="checkOut()">
                Check-out
              </button>
            }
            @if (reservation.status === 'pending' || reservation.status === 'confirmed') {
              <button type="button" class="bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-md hover:bg-red-700" (click)="cancel()">
                Annuler
              </button>
            }
          </div>

          <hr class="border-slate-100" />

          <form [formGroup]="editForm" (ngSubmit)="saveEdits()" class="space-y-3">
            <h2 class="font-semibold text-slate-900">Modifier les dates / voyageurs</h2>
            <div class="grid sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-slate-500 mb-1">Arrivée</label>
                <input type="date" formControlName="check_in_date" class="w-full border rounded-md px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-xs text-slate-500 mb-1">Départ</label>
                <input type="date" formControlName="check_out_date" class="w-full border rounded-md px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-xs text-slate-500 mb-1">Adultes</label>
                <input type="number" min="1" formControlName="number_of_adults" class="w-full border rounded-md px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-xs text-slate-500 mb-1">Enfants</label>
                <input type="number" min="0" formControlName="number_of_children" class="w-full border rounded-md px-3 py-2 text-sm" />
              </div>
            </div>
            <button type="submit" class="bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-md hover:bg-brand-800">
              Enregistrer les modifications
            </button>
          </form>
        </div>
      </div>
    }
  `,
})
export class ReceptionReservationDetail implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(ReceptionReservationService);
  private readonly toast = inject(ToastService);

  protected readonly reservation = signal<Reservation | null>(null);
  protected readonly editForm = this.fb.nonNullable.group({
    check_in_date: [''],
    check_out_date: [''],
    number_of_adults: [1],
    number_of_children: [0],
  });

  private id!: number;

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.refresh();
  }

  private refresh(): void {
    this.service.show(this.id).subscribe((res) => {
      this.reservation.set(res.data);
      this.editForm.patchValue({
        check_in_date: res.data.check_in_date,
        check_out_date: res.data.check_out_date,
        number_of_adults: res.data.number_of_adults,
        number_of_children: res.data.number_of_children,
      });
    });
  }

  protected checkIn(): void {
    this.service.checkIn(this.id).subscribe({
      next: (res) => {
        this.reservation.set(res.data);
        this.toast.success('Check-in effectué.');
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Erreur.'),
    });
  }

  protected checkOut(): void {
    this.service.checkOut(this.id).subscribe({
      next: (res) => {
        this.reservation.set(res.data);
        this.toast.success('Check-out effectué.');
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Erreur.'),
    });
  }

  protected cancel(): void {
    if (!confirm('Annuler cette réservation ?')) return;
    this.service.cancel(this.id).subscribe({
      next: (res) => {
        this.reservation.set(res.data);
        this.toast.success('Réservation annulée.');
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Erreur.'),
    });
  }

  protected saveEdits(): void {
    this.service.update(this.id, this.editForm.getRawValue()).subscribe({
      next: (res) => {
        this.reservation.set(res.data);
        this.toast.success('Réservation mise à jour. Le client a été notifié par email.');
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Impossible de modifier cette réservation.'),
    });
  }
}

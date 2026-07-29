import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { CreateOptions, ReceptionReservationService } from '../../core/services/reception/reservation.service';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-reception-reservation-create',
  imports: [ReactiveFormsModule],
  template: `
    <div class="max-w-2xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Nouvelle réservation (comptoir)</h1>

      @if (options(); as options) {
        <form [formGroup]="form" (ngSubmit)="submit()" class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Client</label>
            <select formControlName="user_id" class="w-full border rounded-md px-3 py-2 text-sm">
              <option [ngValue]="null" disabled>Sélectionner un client</option>
              @for (client of options.clients; track client.id) {
                <option [ngValue]="client.id">{{ client.name }} — {{ client.email }}</option>
              }
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Chambre</label>
            <select formControlName="room_id" class="w-full border rounded-md px-3 py-2 text-sm">
              <option [ngValue]="null" disabled>Sélectionner une chambre disponible</option>
              @for (room of options.available_rooms; track room.id) {
                <option [ngValue]="room.id">Ch. {{ room.room_number }} — {{ room.room_type }} ({{ room.price_per_night }} FCFA/nuit)</option>
              }
            </select>
          </div>
          <div class="grid sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Arrivée</label>
              <input type="date" formControlName="check_in_date" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Départ</label>
              <input type="date" formControlName="check_out_date" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Adultes</label>
              <input type="number" min="1" formControlName="number_of_adults" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Enfants</label>
              <input type="number" min="0" formControlName="number_of_children" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
          </div>

          @if (options.services.length) {
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Services</label>
              <div class="space-y-1">
                @for (service of options.services; track service.id) {
                  <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" [checked]="selectedServices.has(service.id)" (change)="toggleService(service.id)" />
                    {{ service.name }} — {{ service.price }} FCFA
                  </label>
                }
              </div>
            </div>
          }

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Demandes particulières</label>
            <textarea formControlName="special_requests" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
          </div>

          <button
            type="submit"
            class="bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-md hover:bg-brand-800 disabled:opacity-50"
            [disabled]="form.invalid || submitting()"
          >
            Créer la réservation
          </button>
        </form>
      }
    </div>
  `,
})
export class ReceptionReservationCreate implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(ReceptionReservationService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  protected readonly options = signal<CreateOptions | null>(null);
  protected readonly submitting = signal(false);
  protected readonly selectedServices = new Set<number>();

  protected readonly form = this.fb.nonNullable.group({
    user_id: [null as number | null, Validators.required],
    room_id: [null as number | null, Validators.required],
    check_in_date: ['', Validators.required],
    check_out_date: ['', Validators.required],
    number_of_adults: [1, [Validators.required, Validators.min(1)]],
    number_of_children: [0, [Validators.min(0)]],
    special_requests: [''],
  });

  ngOnInit(): void {
    this.service.createOptions().subscribe((res) => this.options.set(res));
  }

  protected toggleService(id: number): void {
    if (this.selectedServices.has(id)) this.selectedServices.delete(id);
    else this.selectedServices.add(id);
  }

  protected submit(): void {
    if (this.form.invalid) return;
    this.submitting.set(true);
    const value = this.form.getRawValue();

    this.service
      .create({
        user_id: value.user_id!,
        room_id: value.room_id!,
        check_in_date: value.check_in_date,
        check_out_date: value.check_out_date,
        number_of_adults: value.number_of_adults,
        number_of_children: value.number_of_children,
        special_requests: value.special_requests || undefined,
        services: Array.from(this.selectedServices),
      })
      .subscribe({
        next: (res) => {
          this.toast.success(`Réservation ${res.data.reservation_number} créée.`);
          this.router.navigate(['/reception/reservations', res.data.id]);
        },
        error: (err) => {
          this.submitting.set(false);
          this.toast.error(err.error?.message ?? 'Impossible de créer la réservation.');
        },
      });
  }
}

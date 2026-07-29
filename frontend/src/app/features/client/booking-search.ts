import { DecimalPipe } from '@angular/common';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { BookingService, SearchMeta } from '../../core/services/booking.service';
import { Room } from '../../core/models/room.model';
import { Pagination } from '../../shared/ui/pagination';

@Component({
  selector: 'app-booking-search',
  imports: [ReactiveFormsModule, DecimalPipe, Pagination],
  template: `
    <div class="max-w-6xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Rechercher une chambre</h1>

      <form [formGroup]="form" (ngSubmit)="search(1)" class="bg-white border border-slate-200 rounded-lg p-4 grid sm:grid-cols-5 gap-3 mb-8">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Arrivée</label>
          <input type="date" formControlName="check_in" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Départ</label>
          <input type="date" formControlName="check_out" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Adultes</label>
          <input type="number" min="1" formControlName="adults" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Enfants</label>
          <input type="number" min="0" formControlName="children" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <button
          type="submit"
          class="self-end bg-brand-700 text-white font-semibold py-2 rounded-md hover:bg-brand-800 disabled:opacity-50"
          [disabled]="form.invalid || loading()"
        >
          Rechercher
        </button>
      </form>

      @if (error()) {
        <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2 mb-6">{{ error() }}</p>
      }

      @if (searched()) {
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @for (room of rooms(); track room.id) {
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
              <div class="h-36 bg-slate-100 flex items-center justify-center text-slate-400">
                @if (room.images.length) {
                  <img [src]="room.images[0].url" class="w-full h-full object-cover" alt="Chambre {{ room.room_number }}" />
                } @else {
                  <span>Pas d'image</span>
                }
              </div>
              <div class="p-4">
                <p class="font-semibold text-slate-900">Chambre {{ room.room_number }} — {{ room.room_type?.name }}</p>
                <p class="text-sm text-slate-500">Jusqu'à {{ room.max_occupancy }} personnes</p>
                <p class="text-brand-700 font-bold mt-1">{{ room.price_per_night | number }} FCFA / nuit</p>
                <button
                  type="button"
                  class="mt-3 w-full bg-brand-700 text-white text-sm font-semibold py-2 rounded-md hover:bg-brand-800"
                  (click)="confirm(room)"
                >
                  Choisir cette chambre
                </button>
              </div>
            </div>
          } @empty {
            <p class="text-slate-500 col-span-full text-center py-12">Aucune chambre disponible pour ces critères.</p>
          }
        </div>

        @if (meta(); as meta) {
          <app-pagination [currentPage]="meta.current_page" [lastPage]="meta.last_page" (pageChange)="search($event)" />
        }
      }
    </div>
  `,
})
export class BookingSearch {
  private readonly fb = inject(FormBuilder);
  private readonly bookingService = inject(BookingService);
  private readonly router = inject(Router);

  protected readonly rooms = signal<Room[]>([]);
  protected readonly meta = signal<SearchMeta | null>(null);
  protected readonly searched = signal(false);
  protected readonly loading = signal(false);
  protected readonly error = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    check_in: [this.tomorrow(), Validators.required],
    check_out: [this.inDays(3), Validators.required],
    adults: [2, [Validators.required, Validators.min(1)]],
    children: [0, [Validators.min(0)]],
  });

  protected search(page: number): void {
    if (this.form.invalid) return;
    this.loading.set(true);
    this.error.set(null);

    this.bookingService.search({ ...this.form.getRawValue(), page }).subscribe({
      next: (res) => {
        this.loading.set(false);
        this.searched.set(true);
        this.rooms.set(res.rooms);
        this.meta.set(res.meta);
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err.error?.message ?? 'Erreur lors de la recherche.');
      },
    });
  }

  protected confirm(room: Room): void {
    const { check_in, check_out, adults, children } = this.form.getRawValue();
    this.router.navigate(['/booking/confirm', room.id], {
      queryParams: { check_in, check_out, adults, children },
    });
  }

  private tomorrow(): string {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().slice(0, 10);
  }

  private inDays(days: number): string {
    const d = new Date();
    d.setDate(d.getDate() + days);
    return d.toISOString().slice(0, 10);
  }
}

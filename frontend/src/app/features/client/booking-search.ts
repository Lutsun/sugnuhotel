import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { BookingService, SearchMeta } from '../../core/services/booking.service';
import { Room } from '../../core/models/room.model';
import { Pagination } from '../../shared/ui/pagination';
import { RoomThumbnail } from '../../shared/ui/room-thumbnail';

@Component({
  selector: 'app-booking-search',
  imports: [ReactiveFormsModule, DecimalPipe, Pagination, RoomThumbnail],
  template: `
    <div class="bg-ink-50/60 py-10 mb-8">
      <div class="max-w-6xl mx-auto px-4">
        <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Réservation</span>
        <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2">Trouvez votre chambre idéale</h1>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 pb-16">
      <form [formGroup]="form" (ngSubmit)="search(1)" class="bg-white border border-ink-100 rounded-xl p-4 grid sm:grid-cols-5 gap-3 mb-8 shadow-sm">
        <div>
          <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Arrivée</label>
          <input type="date" formControlName="check_in" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Départ</label>
          <input type="date" formControlName="check_out" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Adultes</label>
          <input type="number" min="1" formControlName="adults" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Enfants</label>
          <input type="number" min="0" formControlName="children" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
        </div>
        <button
          type="submit"
          class="self-end bg-brand-600 text-white font-semibold py-2.5 rounded-lg hover:bg-brand-700 transition disabled:opacity-50"
          [disabled]="form.invalid || loading()"
        >
          Rechercher
        </button>
      </form>

      @if (error()) {
        <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2 mb-6">{{ error() }}</p>
      }

      @if (searched()) {
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @for (room of rooms(); track room.id) {
            <div class="bg-white rounded-xl border border-ink-100 overflow-hidden">
              <div class="h-36 overflow-hidden">
                <app-room-thumbnail [images]="room.images" [label]="room.room_type?.name ?? 'Chambre'" />
              </div>
              <div class="p-4">
                <p class="font-display font-semibold text-ink-900">Chambre {{ room.room_number }} — {{ room.room_type?.name }}</p>
                <p class="text-sm text-ink-400">Jusqu'à {{ room.max_occupancy }} personnes</p>
                <p class="text-brand-700 font-semibold mt-1">{{ room.price_per_night | number }} FCFA / nuit</p>
                <button
                  type="button"
                  class="mt-3 w-full bg-brand-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-brand-700 transition"
                  (click)="confirm(room)"
                >
                  Choisir cette chambre
                </button>
              </div>
            </div>
          } @empty {
            <p class="text-ink-400 col-span-full text-center py-12">Aucune chambre disponible pour ces critères.</p>
          }
        </div>

        @if (meta(); as meta) {
          <app-pagination [currentPage]="meta.current_page" [lastPage]="meta.last_page" (pageChange)="search($event)" />
        }
      }
    </div>
  `,
})
export class BookingSearch implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly bookingService = inject(BookingService);
  private readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);

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

  ngOnInit(): void {
    const query = this.route.snapshot.queryParamMap;
    const checkIn = query.get('check_in');
    const checkOut = query.get('check_out');
    const adults = query.get('adults');

    if (checkIn || checkOut || adults) {
      this.form.patchValue({
        check_in: checkIn ?? this.form.getRawValue().check_in,
        check_out: checkOut ?? this.form.getRawValue().check_out,
        adults: adults ? Number(adults) : this.form.getRawValue().adults,
      });
      this.search(1);
    }
  }

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

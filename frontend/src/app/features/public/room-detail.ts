import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { Room } from '../../core/models/room.model';
import { RoomService } from '../../core/services/room.service';
import { RoomThumbnail } from '../../shared/ui/room-thumbnail';

@Component({
  selector: 'app-room-detail',
  imports: [RouterLink, DecimalPipe, RoomThumbnail],
  template: `
    @if (room(); as room) {
      <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid md:grid-cols-2 gap-10">
          <div class="h-80 rounded-2xl overflow-hidden shadow-sm">
            <app-room-thumbnail [images]="room.images" [label]="room.room_type?.name ?? 'Chambre'" />
          </div>
          <div>
            <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">{{ room.room_type?.name }}</span>
            <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2">Chambre {{ room.room_number }}</h1>
            <p class="text-ink-400 mt-3 leading-relaxed">{{ room.room_type?.description }}</p>

            <ul class="mt-6 grid grid-cols-2 gap-3 text-sm text-ink-700">
              <li class="bg-ink-50/60 rounded-lg px-3 py-2">Étage {{ room.floor }}</li>
              <li class="bg-ink-50/60 rounded-lg px-3 py-2">{{ room.max_occupancy }} personnes max.</li>
              <li class="bg-ink-50/60 rounded-lg px-3 py-2 col-span-2 capitalize">Statut : {{ room.status }}</li>
            </ul>

            <p class="text-brand-700 font-display font-semibold text-3xl mt-8">
              {{ room.price_per_night | number }} FCFA <span class="text-ink-400 font-sans font-normal text-base">/ nuit</span>
            </p>

            <button
              type="button"
              class="mt-6 bg-brand-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-brand-700 transition shadow-sm shadow-brand-600/30"
              (click)="reserve()"
            >
              Réserver cette chambre
            </button>
          </div>
        </div>

        @if (similarRooms().length) {
          <div class="mt-16">
            <h2 class="font-display text-2xl font-semibold text-ink-900 mb-6">Chambres similaires</h2>
            <div class="grid sm:grid-cols-3 gap-6">
              @for (similar of similarRooms(); track similar.id) {
                <a [routerLink]="['/rooms', similar.id]" class="bg-white rounded-xl border border-ink-100 overflow-hidden hover:shadow-lg transition">
                  <div class="h-32 overflow-hidden">
                    <app-room-thumbnail [images]="similar.images" [label]="similar.room_type?.name ?? 'Chambre'" />
                  </div>
                  <div class="p-4">
                    <p class="font-semibold text-ink-900">Chambre {{ similar.room_number }}</p>
                    <p class="text-brand-700 font-semibold mt-1">{{ similar.price_per_night | number }} FCFA / nuit</p>
                  </div>
                </a>
              }
            </div>
          </div>
        }
      </div>
    }
  `,
})
export class RoomDetail implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly roomService = inject(RoomService);
  protected readonly auth = inject(AuthService);

  protected readonly room = signal<Room | null>(null);
  protected readonly similarRooms = signal<Room[]>([]);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.roomService.show(id).subscribe((res) => {
      this.room.set(res.room);
      this.similarRooms.set(res.similar_rooms);
    });
  }

  protected reserve(): void {
    if (!this.auth.isAuthenticated()) {
      this.router.navigateByUrl('/login');
      return;
    }
    this.router.navigate(['/booking/search'], { queryParams: { room_id: this.room()?.id } });
  }
}

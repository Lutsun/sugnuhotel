import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { Room } from '../../core/models/room.model';
import { RoomService } from '../../core/services/room.service';

@Component({
  selector: 'app-room-detail',
  imports: [RouterLink, DecimalPipe],
  template: `
    @if (room(); as room) {
      <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="grid md:grid-cols-2 gap-8">
          <div class="h-80 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center text-slate-400">
            @if (room.images.length) {
              <img [src]="room.images[0].url" class="w-full h-full object-cover" alt="Chambre {{ room.room_number }}" />
            } @else {
              <span>Pas d'image</span>
            }
          </div>
          <div>
            <h1 class="text-2xl font-bold text-slate-900">Chambre {{ room.room_number }} — {{ room.room_type?.name }}</h1>
            <p class="text-slate-600 mt-2">{{ room.room_type?.description }}</p>
            <ul class="mt-4 text-sm text-slate-600 space-y-1">
              <li>Étage : {{ room.floor }}</li>
              <li>Capacité : {{ room.max_occupancy }} personnes</li>
              <li>Statut : {{ room.status }}</li>
            </ul>
            <p class="text-brand-700 font-bold text-2xl mt-6">{{ room.price_per_night | number }} FCFA / nuit</p>

            <button
              type="button"
              class="mt-6 bg-brand-700 text-white font-semibold px-6 py-3 rounded-md hover:bg-brand-800"
              (click)="reserve()"
            >
              Réserver cette chambre
            </button>
          </div>
        </div>

        @if (similarRooms().length) {
          <div class="mt-12">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Chambres similaires</h2>
            <div class="grid sm:grid-cols-3 gap-6">
              @for (similar of similarRooms(); track similar.id) {
                <a [routerLink]="['/rooms', similar.id]" class="bg-white rounded-lg border border-slate-200 p-4 hover:shadow-md transition">
                  <p class="font-semibold text-slate-900">Chambre {{ similar.room_number }}</p>
                  <p class="text-brand-700 font-bold mt-1">{{ similar.price_per_night | number }} FCFA / nuit</p>
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

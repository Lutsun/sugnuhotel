import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { RoomService } from '../../core/services/room.service';
import { Room, RoomType } from '../../core/models/room.model';
import { Pagination } from '../../shared/ui/pagination';
import { RoomThumbnail } from '../../shared/ui/room-thumbnail';

@Component({
  selector: 'app-rooms-list',
  imports: [RouterLink, FormsModule, DecimalPipe, Pagination, RoomThumbnail],
  template: `
    <div class="bg-ink-50/60 py-10 mb-2">
      <div class="max-w-6xl mx-auto px-4">
        <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Nos hébergements</span>
        <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2">Toutes nos chambres</h1>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 pb-16">
      <div class="bg-white border border-ink-100 rounded-xl p-4 mb-8 grid sm:grid-cols-4 gap-3 shadow-sm">
        <select class="border border-ink-100 rounded-lg px-3 py-2 text-sm" [(ngModel)]="roomTypeId" (ngModelChange)="load(1)">
          <option [ngValue]="undefined">Tous les types</option>
          @for (type of roomTypes(); track type.id) {
            <option [ngValue]="type.id">{{ type.name }}</option>
          }
        </select>
        <input type="number" placeholder="Capacité min." class="border border-ink-100 rounded-lg px-3 py-2 text-sm" [(ngModel)]="capacity" (ngModelChange)="load(1)" />
        <input type="number" placeholder="Prix min." class="border border-ink-100 rounded-lg px-3 py-2 text-sm" [(ngModel)]="priceMin" (ngModelChange)="load(1)" />
        <input type="number" placeholder="Prix max." class="border border-ink-100 rounded-lg px-3 py-2 text-sm" [(ngModel)]="priceMax" (ngModelChange)="load(1)" />
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @for (room of rooms(); track room.id) {
          <a [routerLink]="['/rooms', room.id]" class="bg-white rounded-xl border border-ink-100 overflow-hidden hover:shadow-lg transition">
            <div class="h-44 overflow-hidden">
              <app-room-thumbnail [images]="room.images" [label]="room.room_type?.name ?? 'Chambre'" />
            </div>
            <div class="p-5">
              <p class="font-display font-semibold text-ink-900">Chambre {{ room.room_number }} — {{ room.room_type?.name }}</p>
              <p class="text-sm text-ink-400 mt-1">Jusqu'à {{ room.max_occupancy }} personnes</p>
              <p class="text-brand-700 font-semibold mt-2">{{ room.price_per_night | number }} FCFA <span class="text-ink-400 font-normal text-sm">/ nuit</span></p>
            </div>
          </a>
        } @empty {
          <p class="text-ink-400 col-span-full text-center py-12">Aucune chambre ne correspond à ces critères.</p>
        }
      </div>

      <app-pagination [currentPage]="currentPage()" [lastPage]="lastPage()" (pageChange)="load($event)" />
    </div>
  `,
})
export class RoomsList implements OnInit {
  private readonly roomService = inject(RoomService);
  private readonly route = inject(ActivatedRoute);

  protected readonly rooms = signal<Room[]>([]);
  protected readonly roomTypes = signal<RoomType[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);

  protected roomTypeId?: number;
  protected capacity?: number;
  protected priceMin?: number;
  protected priceMax?: number;

  ngOnInit(): void {
    this.roomService.roomTypes().subscribe((res) => this.roomTypes.set(res.data));

    const queryRoomType = this.route.snapshot.queryParamMap.get('room_type');
    if (queryRoomType) {
      this.roomTypeId = Number(queryRoomType);
    }

    this.load(1);
  }

  protected load(page: number): void {
    this.roomService
      .list({
        room_type: this.roomTypeId,
        capacity: this.capacity,
        price_min: this.priceMin,
        price_max: this.priceMax,
        page,
      })
      .subscribe((res) => {
        this.rooms.set(res.data);
        this.currentPage.set(res.meta.current_page);
        this.lastPage.set(res.meta.last_page);
      });
  }
}

import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { RoomService } from '../../core/services/room.service';
import { Room, RoomType } from '../../core/models/room.model';
import { Pagination } from '../../shared/ui/pagination';

@Component({
  selector: 'app-rooms-list',
  imports: [RouterLink, FormsModule, DecimalPipe, Pagination],
  template: `
    <div class="max-w-6xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Nos chambres</h1>

      <div class="bg-white border border-slate-200 rounded-lg p-4 mb-8 grid sm:grid-cols-4 gap-3">
        <select class="border rounded-md px-3 py-2 text-sm" [(ngModel)]="roomTypeId" (ngModelChange)="load(1)">
          <option [ngValue]="undefined">Tous les types</option>
          @for (type of roomTypes(); track type.id) {
            <option [ngValue]="type.id">{{ type.name }}</option>
          }
        </select>
        <input type="number" placeholder="Capacité min." class="border rounded-md px-3 py-2 text-sm" [(ngModel)]="capacity" (ngModelChange)="load(1)" />
        <input type="number" placeholder="Prix min." class="border rounded-md px-3 py-2 text-sm" [(ngModel)]="priceMin" (ngModelChange)="load(1)" />
        <input type="number" placeholder="Prix max." class="border rounded-md px-3 py-2 text-sm" [(ngModel)]="priceMax" (ngModelChange)="load(1)" />
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @for (room of rooms(); track room.id) {
          <a [routerLink]="['/rooms', room.id]" class="bg-white rounded-lg border border-slate-200 overflow-hidden hover:shadow-md transition">
            <div class="h-40 bg-slate-100 flex items-center justify-center text-slate-400">
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
            </div>
          </a>
        } @empty {
          <p class="text-slate-500 col-span-full text-center py-12">Aucune chambre ne correspond à ces critères.</p>
        }
      </div>

      <app-pagination [currentPage]="currentPage()" [lastPage]="lastPage()" (pageChange)="load($event)" />
    </div>
  `,
})
export class RoomsList implements OnInit {
  private readonly roomService = inject(RoomService);

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

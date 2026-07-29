import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { RoomService, HomeData } from '../../core/services/room.service';

@Component({
  selector: 'app-home',
  imports: [RouterLink, DecimalPipe],
  template: `
    <section class="bg-brand-700 text-white">
      <div class="max-w-6xl mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl font-bold mb-4">Bienvenue à SugnuHotel</h1>
        <p class="text-brand-100 mb-8 text-lg">Confort et hospitalité sénégalaise, au cœur de Dakar.</p>
        <a routerLink="/rooms" class="bg-white text-brand-700 font-semibold px-6 py-3 rounded-md hover:bg-brand-50">
          Voir nos chambres
        </a>
      </div>
    </section>

    @if (data(); as home) {
      <section class="max-w-6xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Nos types de chambres</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
          @for (type of home.room_types; track type.id) {
            <div class="bg-white rounded-lg border border-slate-200 p-5">
              <h3 class="font-semibold text-slate-900">{{ type.name }}</h3>
              <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ type.description }}</p>
              <p class="mt-3 text-brand-700 font-bold">{{ type.base_price | number }} FCFA / nuit</p>
            </div>
          }
        </div>
      </section>

      <section class="max-w-6xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Chambres disponibles</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @for (room of home.rooms; track room.id) {
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
                <p class="text-brand-700 font-bold mt-1">{{ room.price_per_night | number }} FCFA / nuit</p>
              </div>
            </a>
          }
        </div>
      </section>

      <section class="max-w-6xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Services additionnels</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
          @for (service of home.services; track service.id) {
            <div class="bg-white rounded-lg border border-slate-200 p-4">
              <p class="font-semibold text-slate-900">{{ service.name }}</p>
              <p class="text-sm text-slate-500 mt-1">{{ service.price | number }} FCFA</p>
            </div>
          }
        </div>
      </section>
    }
  `,
})
export class Home implements OnInit {
  private readonly roomService = inject(RoomService);
  protected readonly data = signal<HomeData | null>(null);

  ngOnInit(): void {
    this.roomService.home().subscribe((home) => this.data.set(home));
  }
}

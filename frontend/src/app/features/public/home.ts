import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { RoomService, HomeData } from '../../core/services/room.service';
import { RoomThumbnail } from '../../shared/ui/room-thumbnail';
import { roomTypeImage } from '../../shared/ui/room-type-images';

@Component({
  selector: 'app-home',
  imports: [RouterLink, DecimalPipe, FormsModule, RoomThumbnail],
  template: `
    <!-- Hero -->
    <section
      class="relative bg-ink-900 overflow-hidden bg-cover bg-center min-h-screen flex flex-col items-center justify-start"
      style="background-image: linear-gradient(180deg, rgba(10,12,18,.35) 0%, rgba(10,12,18,.35) 45%, rgba(10,12,18,.85) 100%), url('/images/hero-main.jpg')"
    >
      <div class="max-w-6xl mx-auto px-4 pt-6 sm:pt-10 pb-10 sm:pb-32 text-center relative z-10 w-full">
        <span class="inline-block text-brand-300 text-xs font-semibold tracking-[0.2em] uppercase mb-5">Bienvenue à Dakar</span>
        <h1 class="font-display text-5xl sm:text-7xl font-semibold text-white text-balance leading-tight mb-6 [text-shadow:0_2px_24px_rgba(0,0,0,.35)]">
          L'hospitalité sénégalaise,<br class="hidden sm:block" /> réinventée pour vous
        </h1>
        <p class="text-white/85 text-lg sm:text-xl max-w-xl mx-auto mb-10">
          Chambres élégantes, service attentionné et une adresse au cœur de la ville. SugnuHotel vous accueille comme chez vous.
        </p>
        <div class="flex items-center justify-center gap-3 flex-wrap">
          <a routerLink="/rooms" class="bg-white text-ink-900 font-semibold px-7 py-3.5 rounded-full hover:bg-brand-50 transition shadow-lg">
            Découvrir nos chambres
          </a>
          <a routerLink="/booking/search" class="border border-white/40 text-white font-semibold px-7 py-3.5 rounded-full hover:bg-white/10 transition backdrop-blur-sm">
            Réserver maintenant
          </a>
        </div>
      </div>

      <!-- Quick search card : en flux normal sur mobile, ancrée sur la photo à partir de sm -->
      <div class="relative sm:absolute inset-x-0 sm:bottom-12 z-10 px-4 pb-10 sm:pb-0 w-full">
        <form
          (ngSubmit)="quickSearch()"
          class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl shadow-black/30 p-5 grid sm:grid-cols-4 gap-3"
        >
          <div class="text-left">
            <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Arrivée</label>
            <input type="date" [(ngModel)]="checkIn" name="checkIn" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
          </div>
          <div class="text-left">
            <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Départ</label>
            <input type="date" [(ngModel)]="checkOut" name="checkOut" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
          </div>
          <div class="text-left">
            <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1">Voyageurs</label>
            <input type="number" min="1" [(ngModel)]="adults" name="adults" class="w-full border border-ink-100 rounded-lg px-3 py-2 text-sm" />
          </div>
          <button
            type="submit"
            class="self-end bg-brand-600 text-white font-semibold rounded-lg py-2.5 hover:bg-brand-700 transition"
          >
            Vérifier la disponibilité
          </button>
        </form>
      </div>
    </section>

    <!-- Points forts -->
    <section class="max-w-6xl mx-auto px-4 pt-16 pb-20">
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center px-4">
          <div class="w-14 h-14 mx-auto rounded-full bg-brand-50 text-brand-700 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5-4-7-7.5-7-11a7 7 0 1114 0c0 3.5-2.5 7-7 11z"/>
              <circle cx="12" cy="10" r="2.5"/>
            </svg>
          </div>
          <h3 class="font-display text-lg font-semibold text-ink-900 mb-1">Emplacement central</h3>
          <p class="text-sm text-ink-400">Au cœur de Dakar, proche des principaux points d'intérêt.</p>
        </div>
        <div class="text-center px-4">
          <div class="w-14 h-14 mx-auto rounded-full bg-brand-50 text-brand-700 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
              <circle cx="12" cy="12" r="9"/>
            </svg>
          </div>
          <h3 class="font-display text-lg font-semibold text-ink-900 mb-1">Réception 24h/24</h3>
          <p class="text-sm text-ink-400">Notre équipe est disponible à toute heure pour vous accueillir.</p>
        </div>
        <div class="text-center px-4">
          <div class="w-14 h-14 mx-auto rounded-full bg-brand-50 text-brand-700 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18h18M6 10V7a2 2 0 012-2h1a2 2 0 012 2v3"/>
            </svg>
          </div>
          <h3 class="font-display text-lg font-semibold text-ink-900 mb-1">Chambres tout confort</h3>
          <p class="text-sm text-ink-400">Literie haut de gamme, propreté irréprochable, équipements modernes.</p>
        </div>
        <div class="text-center px-4">
          <div class="w-14 h-14 mx-auto rounded-full bg-brand-50 text-brand-700 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8M5 12a7 7 0 1114 0 7 7 0 01-14 0z"/>
            </svg>
          </div>
          <h3 class="font-display text-lg font-semibold text-ink-900 mb-1">Meilleur prix garanti</h3>
          <p class="text-sm text-ink-400">Un rapport qualité-prix pensé pour tous les voyageurs.</p>
        </div>
      </div>
    </section>

    <!-- Ambiance -->
    <section class="relative h-72 sm:h-96 bg-cover bg-center" style="background-image: url('/images/pool.jpg')">
      <div class="absolute inset-0 bg-gradient-to-t from-ink-900/70 via-ink-900/10 to-transparent"></div>
      <div class="relative h-full max-w-6xl mx-auto px-4 flex items-end pb-8">
        <div>
          <span class="text-brand-200 text-xs font-semibold tracking-[0.2em] uppercase">L'expérience SugnuHotel</span>
          <h2 class="font-display text-2xl sm:text-3xl font-semibold text-white mt-2 text-balance">
            Un cadre pensé pour la détente et le repos
          </h2>
        </div>
      </div>
    </section>

    <!-- Types de chambres -->
    @if (data(); as home) {
      <section class="bg-ink-50/60 py-20">
        <div class="max-w-6xl mx-auto px-4">
          <div class="text-center mb-12">
            <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Nos hébergements</span>
            <h2 class="font-display text-3xl font-semibold text-ink-900 mt-2">Un type de chambre pour chaque envie</h2>
          </div>
          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @for (type of home.room_types; track type.id) {
              <a
                [routerLink]="['/rooms']"
                [queryParams]="{ room_type: type.id }"
                class="bg-white rounded-xl border border-ink-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition block"
              >
                <div class="h-36 overflow-hidden">
                  @if (roomTypeImage(type.name); as photo) {
                    <img [src]="photo" [alt]="type.name" class="w-full h-full object-cover" />
                  } @else {
                    <div class="w-full h-full bg-gradient-to-br from-brand-100 via-brand-50 to-white"></div>
                  }
                </div>
                <div class="p-6">
                  <h3 class="font-display font-semibold text-lg text-ink-900">{{ type.name }}</h3>
                  <p class="text-sm text-ink-400 mt-2 line-clamp-2">{{ type.description }}</p>
                  <p class="mt-4 text-brand-700 font-semibold">{{ type.base_price | number }} FCFA <span class="text-ink-400 font-normal text-sm">/ nuit</span></p>
                </div>
              </a>
            }
          </div>
        </div>
      </section>

      <!-- Chambres disponibles -->
      <section class="max-w-6xl mx-auto px-4 py-20">
        <div class="flex items-end justify-between mb-10 flex-wrap gap-3">
          <div>
            <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Disponibles maintenant</span>
            <h2 class="font-display text-3xl font-semibold text-ink-900 mt-2">Chambres prêtes à vous accueillir</h2>
          </div>
          <a routerLink="/rooms" class="text-brand-700 font-semibold text-sm hover:underline">Voir toutes les chambres →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @for (room of home.rooms; track room.id) {
            <a [routerLink]="['/rooms', room.id]" class="bg-white rounded-xl border border-ink-100 overflow-hidden hover:shadow-lg transition group">
              <div class="h-44 overflow-hidden">
                <app-room-thumbnail [images]="room.images" [label]="room.room_type?.name ?? 'Chambre'" />
              </div>
              <div class="p-5">
                <p class="font-display font-semibold text-ink-900">Chambre {{ room.room_number }} — {{ room.room_type?.name }}</p>
                <p class="text-sm text-ink-400 mt-1">Jusqu'à {{ room.max_occupancy }} personnes</p>
                <p class="text-brand-700 font-semibold mt-2">{{ room.price_per_night | number }} FCFA <span class="text-ink-400 font-normal text-sm">/ nuit</span></p>
              </div>
            </a>
          }
        </div>
      </section>

      <!-- Services -->
      <section class="bg-ink-50/60 py-20">
        <div class="max-w-6xl mx-auto px-4">
          <div class="text-center mb-12">
            <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Sur mesure</span>
            <h2 class="font-display text-3xl font-semibold text-ink-900 mt-2">Des services pensés pour votre confort</h2>
          </div>
          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @for (service of home.services; track service.id) {
              <div class="bg-white rounded-xl border border-ink-100 p-5">
                <p class="font-semibold text-ink-900">{{ service.name }}</p>
                <p class="text-sm text-brand-700 font-medium mt-1">{{ service.price | number }} FCFA</p>
              </div>
            }
          </div>
        </div>
      </section>
    }

    <!-- CTA final -->
    <section class="bg-ink-900 hero-pattern">
      <div class="max-w-4xl mx-auto px-4 py-20 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-semibold text-white mb-4 text-balance">
          Prêt à vivre l'expérience SugnuHotel ?
        </h2>
        <p class="text-ink-100/80 mb-8">Créez votre compte et réservez votre chambre en quelques minutes.</p>
        <a routerLink="/register" class="bg-brand-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-brand-700 transition inline-block">
          Créer mon compte
        </a>
      </div>
    </section>
  `,
})
export class Home implements OnInit {
  private readonly roomService = inject(RoomService);
  private readonly router = inject(Router);
  protected readonly auth = inject(AuthService);
  protected readonly data = signal<HomeData | null>(null);
  protected readonly roomTypeImage = roomTypeImage;

  protected checkIn = this.tomorrow();
  protected checkOut = this.inDays(3);
  protected adults = 2;


  ngOnInit(): void {
    this.roomService.home().subscribe((home) => this.data.set(home));
  }

  protected quickSearch(): void {
    if (!this.auth.isAuthenticated()) {
      this.router.navigateByUrl('/login');
      return;
    }
    this.router.navigate(['/booking/search'], {
      queryParams: { check_in: this.checkIn, check_out: this.checkOut, adults: this.adults },
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

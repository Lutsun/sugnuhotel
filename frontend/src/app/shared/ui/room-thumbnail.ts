import { Component, Input } from '@angular/core';
import { RoomImage } from '../../core/models/room.model';
import { roomTypeImage } from './room-type-images';

@Component({
  selector: 'app-room-thumbnail',
  template: `
    @if (images && images.length) {
      <img [src]="images[0].url" class="w-full h-full object-cover" [alt]="label" />
    } @else if (fallbackPhoto) {
      <img [src]="fallbackPhoto" class="w-full h-full object-cover" [alt]="label" />
    } @else {
      <div class="w-full h-full bg-gradient-to-br from-brand-100 via-brand-50 to-white flex flex-col items-center justify-center gap-2 text-brand-700">
        <svg class="w-9 h-9 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18h18M3 18v2M21 18v2M6 10V7a2 2 0 012-2h1a2 2 0 012 2v3" />
        </svg>
        <span class="text-xs font-medium opacity-80">{{ label }}</span>
      </div>
    }
  `,
})
export class RoomThumbnail {
  @Input() images: RoomImage[] | undefined | null;
  @Input() label = 'SugnuHotel';
  @Input() roomTypeName: string | null | undefined;

  protected get fallbackPhoto(): string | null {
    return roomTypeImage(this.roomTypeName ?? this.label);
  }
}

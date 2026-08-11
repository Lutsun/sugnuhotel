import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AdminRoomService } from '../../core/services/admin/room.service';
import { RoomService } from '../../core/services/room.service';
import { Room, RoomStatus, RoomType } from '../../core/models/room.model';
import { Pagination } from '../../shared/ui/pagination';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-admin-rooms',
  imports: [ReactiveFormsModule, DecimalPipe, Pagination],
  template: `
    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Chambres</h1>
        <button type="button" class="bg-brand-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-brand-800" (click)="openCreate()">
          + Nouvelle chambre
        </button>
      </div>

      @if (formOpen()) {
        <form [formGroup]="form" (ngSubmit)="save()" class="bg-white border border-slate-200 rounded-lg p-6 mb-6 space-y-3">
          <h2 class="font-semibold text-slate-900">{{ editingId() ? 'Modifier' : 'Créer' }} une chambre</h2>
          <div class="grid sm:grid-cols-2 gap-3">
            <input type="text" placeholder="Numéro de chambre" formControlName="room_number" class="border rounded-md px-3 py-2 text-sm" />
            <select formControlName="room_type_id" class="border rounded-md px-3 py-2 text-sm">
              <option [ngValue]="null" disabled>Type de chambre</option>
              @for (type of roomTypes(); track type.id) {
                <option [ngValue]="type.id">{{ type.name }}</option>
              }
            </select>
            <input type="number" placeholder="Étage" formControlName="floor" class="border rounded-md px-3 py-2 text-sm" />
            <input type="number" placeholder="Prix / nuit" formControlName="price_per_night" class="border rounded-md px-3 py-2 text-sm" />
            <input type="number" placeholder="Capacité max." formControlName="max_occupancy" class="border rounded-md px-3 py-2 text-sm" />
            <select formControlName="status" class="border rounded-md px-3 py-2 text-sm">
              <option value="available">Disponible</option>
              <option value="occupied">Occupée</option>
              <option value="maintenance">Maintenance</option>
              <option value="out_of_service">Hors service</option>
            </select>
          </div>

          @if (!editingId()) {
            <input type="file" accept="image/*" multiple (change)="onFilesSelected($event)" class="text-sm" />
          }

          @if (editingId(); as id) {
            <div>
              <p class="text-sm font-medium text-slate-700 mb-2">Photos</p>
              <div class="flex flex-wrap gap-2 mb-2">
                @for (image of editingImages(); track image.id) {
                  <div class="relative">
                    <img [src]="image.url" class="w-20 h-20 object-cover rounded-md border" />
                    <button
                      type="button"
                      class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 text-xs"
                      (click)="deleteImage(image.id)"
                    >
                      ✕
                    </button>
                  </div>
                }
              </div>
              <input type="file" accept="image/*" (change)="uploadImage($event, id)" class="text-sm" />
            </div>
          }

          <div class="flex gap-2">
            <button type="submit" class="bg-brand-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-brand-800" [disabled]="form.invalid">
              Enregistrer
            </button>
            <button type="button" class="border px-4 py-2 rounded-md text-sm" (click)="formOpen.set(false)">Annuler</button>
          </div>
        </form>
      }

      <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-2">N°</th>
              <th class="px-4 py-2">Type</th>
              <th class="px-4 py-2">Étage</th>
              <th class="px-4 py-2">Prix</th>
              <th class="px-4 py-2">Statut</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @for (room of rooms(); track room.id) {
              <tr class="border-t border-slate-100">
                <td class="px-4 py-2">{{ room.room_number }}</td>
                <td class="px-4 py-2">{{ room.room_type?.name }}</td>
                <td class="px-4 py-2">{{ room.floor }}</td>
                <td class="px-4 py-2">{{ room.price_per_night | number }} FCFA</td>
                <td class="px-4 py-2">{{ room.status }}</td>
                <td class="px-4 py-2 text-right space-x-2">
                  <button type="button" class="text-brand-700 hover:underline" (click)="openEdit(room)">Modifier</button>
                  <button type="button" class="text-red-600 hover:underline" (click)="remove(room)">Supprimer</button>
                </td>
              </tr>
            }
          </tbody>
        </table>
      </div>

      <app-pagination [currentPage]="currentPage()" [lastPage]="lastPage()" (pageChange)="load($event)" />
    </div>
  `,
})
export class AdminRooms implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(AdminRoomService);
  private readonly roomService = inject(RoomService);
  private readonly toast = inject(ToastService);

  protected readonly rooms = signal<Room[]>([]);
  protected readonly roomTypes = signal<RoomType[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);
  protected readonly formOpen = signal(false);
  protected readonly editingId = signal<number | null>(null);
  protected readonly editingImages = signal<Room['images']>([]);
  private selectedFiles: FileList | null = null;

  protected readonly form = this.fb.nonNullable.group({
    room_number: ['', Validators.required],
    room_type_id: [null as number | null, Validators.required],
    floor: [0, Validators.required],
    price_per_night: [0, [Validators.required, Validators.min(0)]],
    max_occupancy: [1, [Validators.required, Validators.min(1)]],
    status: ['available', Validators.required],
  });

  ngOnInit(): void {
    this.roomService.roomTypes().subscribe((res) => this.roomTypes.set(res.data));
    this.load(1);
  }

  protected load(page: number): void {
    this.service.list({ page }).subscribe((res) => {
      this.rooms.set(res.data);
      this.currentPage.set(res.meta.current_page);
      this.lastPage.set(res.meta.last_page);
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.editingImages.set([]);
    this.form.reset({ room_number: '', room_type_id: null, floor: 0, price_per_night: 0, max_occupancy: 1, status: 'available' });
    this.selectedFiles = null;
    this.formOpen.set(true);
  }

  protected openEdit(room: Room): void {
    this.editingId.set(room.id);
    this.editingImages.set(room.images);
    this.form.patchValue({
      room_number: room.room_number,
      room_type_id: room.room_type_id,
      floor: room.floor,
      price_per_night: room.price_per_night,
      max_occupancy: room.max_occupancy,
      status: room.status,
    });
    this.formOpen.set(true);
  }

  protected onFilesSelected(event: Event): void {
    this.selectedFiles = (event.target as HTMLInputElement).files;
  }

  protected save(): void {
    if (this.form.invalid) return;
    const value = this.form.getRawValue();
    const id = this.editingId();

    if (id) {
      this.service.update(id, { ...value, room_type_id: value.room_type_id!, status: value.status as RoomStatus }).subscribe({
        next: () => {
          this.toast.success('Chambre mise à jour.');
          this.formOpen.set(false);
          this.load(this.currentPage());
        },
        error: (err) => this.toast.error(err.error?.message ?? 'Erreur.'),
      });
      return;
    }

    const formData = new FormData();
    Object.entries(value).forEach(([key, val]) => formData.append(key, String(val)));
    if (this.selectedFiles) {
      Array.from(this.selectedFiles).forEach((file) => formData.append('images[]', file));
    }

    this.service.create(formData).subscribe({
      next: () => {
        this.toast.success('Chambre créée.');
        this.formOpen.set(false);
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Erreur.'),
    });
  }

  protected uploadImage(event: Event, roomId: number): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.service.uploadImage(roomId, file).subscribe((res) => {
      this.editingImages.set(res.data.images);
      this.toast.success('Photo ajoutée.');
    });
  }

  protected deleteImage(imageId: number): void {
    this.service.deleteImage(imageId).subscribe(() => {
      this.editingImages.update((list) => list.filter((img) => img.id !== imageId));
      this.toast.success('Photo supprimée.');
    });
  }

  protected remove(room: Room): void {
    if (!confirm(`Supprimer la chambre ${room.room_number} ?`)) return;
    this.service.delete(room.id).subscribe({
      next: () => {
        this.toast.success('Chambre supprimée.');
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Suppression impossible.'),
    });
  }
}

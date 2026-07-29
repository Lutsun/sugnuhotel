import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AdminRoomTypeService } from '../../core/services/admin/room-type.service';
import { RoomType } from '../../core/models/room.model';
import { Pagination } from '../../shared/ui/pagination';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-admin-room-types',
  imports: [ReactiveFormsModule, DecimalPipe, Pagination],
  template: `
    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Types de chambres</h1>
        <button type="button" class="bg-brand-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-brand-800" (click)="openCreate()">
          + Nouveau type
        </button>
      </div>

      @if (formOpen()) {
        <form [formGroup]="form" (ngSubmit)="save()" class="bg-white border border-slate-200 rounded-lg p-6 mb-6 space-y-3">
          <h2 class="font-semibold text-slate-900">{{ editingId() ? 'Modifier' : 'Créer' }} un type de chambre</h2>
          <input type="text" placeholder="Nom" formControlName="name" class="w-full border rounded-md px-3 py-2 text-sm" />
          <textarea placeholder="Description" formControlName="description" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
          <div class="grid grid-cols-2 gap-3">
            <input type="number" placeholder="Prix de base" formControlName="base_price" class="border rounded-md px-3 py-2 text-sm" />
            <input type="number" placeholder="Capacité max." formControlName="max_occupancy" class="border rounded-md px-3 py-2 text-sm" />
          </div>
          <input type="file" accept="image/*" (change)="onFileSelected($event)" class="text-sm" />
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
              <th class="px-4 py-2">Nom</th>
              <th class="px-4 py-2">Prix de base</th>
              <th class="px-4 py-2">Capacité</th>
              <th class="px-4 py-2">Chambres</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @for (type of roomTypes(); track type.id) {
              <tr class="border-t border-slate-100">
                <td class="px-4 py-2">{{ type.name }}</td>
                <td class="px-4 py-2">{{ type.base_price | number }} FCFA</td>
                <td class="px-4 py-2">{{ type.max_occupancy }}</td>
                <td class="px-4 py-2">{{ type.rooms_count }}</td>
                <td class="px-4 py-2 text-right space-x-2">
                  <button type="button" class="text-brand-700 hover:underline" (click)="openEdit(type)">Modifier</button>
                  <button type="button" class="text-red-600 hover:underline" (click)="remove(type)">Supprimer</button>
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
export class AdminRoomTypes implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(AdminRoomTypeService);
  private readonly toast = inject(ToastService);

  protected readonly roomTypes = signal<RoomType[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);
  protected readonly formOpen = signal(false);
  protected readonly editingId = signal<number | null>(null);
  private selectedFile: File | null = null;

  protected readonly form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    description: [''],
    base_price: [0, [Validators.required, Validators.min(0)]],
    max_occupancy: [1, [Validators.required, Validators.min(1)]],
  });

  ngOnInit(): void {
    this.load(1);
  }

  protected load(page: number): void {
    this.service.list(page).subscribe((res) => {
      this.roomTypes.set(res.data);
      this.currentPage.set(res.meta.current_page);
      this.lastPage.set(res.meta.last_page);
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset({ name: '', description: '', base_price: 0, max_occupancy: 1 });
    this.selectedFile = null;
    this.formOpen.set(true);
  }

  protected openEdit(type: RoomType): void {
    this.editingId.set(type.id);
    this.form.patchValue({
      name: type.name,
      description: type.description ?? '',
      base_price: type.base_price,
      max_occupancy: type.max_occupancy,
    });
    this.selectedFile = null;
    this.formOpen.set(true);
  }

  protected onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    this.selectedFile = input.files?.[0] ?? null;
  }

  protected save(): void {
    if (this.form.invalid) return;
    const value = this.form.getRawValue();
    const formData = new FormData();
    formData.append('name', value.name);
    formData.append('description', value.description ?? '');
    formData.append('base_price', String(value.base_price));
    formData.append('max_occupancy', String(value.max_occupancy));
    if (this.selectedFile) formData.append('image', this.selectedFile);

    const id = this.editingId();
    const request = id ? this.service.update(id, formData) : this.service.create(formData);

    request.subscribe({
      next: () => {
        this.toast.success('Type de chambre enregistré.');
        this.formOpen.set(false);
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Erreur lors de l’enregistrement.'),
    });
  }

  protected remove(type: RoomType): void {
    if (!confirm(`Supprimer le type "${type.name}" ?`)) return;
    this.service.delete(type.id).subscribe({
      next: () => {
        this.toast.success('Type de chambre supprimé.');
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Suppression impossible.'),
    });
  }
}

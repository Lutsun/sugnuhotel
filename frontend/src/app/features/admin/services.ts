import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AdminServiceService } from '../../core/services/admin/service.service';
import { Service } from '../../core/models/service.model';
import { Pagination } from '../../shared/ui/pagination';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-admin-services',
  imports: [ReactiveFormsModule, DecimalPipe, Pagination],
  template: `
    <div class="max-w-4xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Services</h1>
        <button type="button" class="bg-brand-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-brand-800" (click)="openCreate()">
          + Nouveau service
        </button>
      </div>

      @if (formOpen()) {
        <form [formGroup]="form" (ngSubmit)="save()" class="bg-white border border-slate-200 rounded-lg p-6 mb-6 space-y-3">
          <h2 class="font-semibold text-slate-900">{{ editingId() ? 'Modifier' : 'Créer' }} un service</h2>
          <input type="text" placeholder="Nom" formControlName="name" class="w-full border rounded-md px-3 py-2 text-sm" />
          <textarea placeholder="Description" formControlName="description" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
          <input type="number" placeholder="Prix" formControlName="price" class="w-full border rounded-md px-3 py-2 text-sm" />
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" formControlName="is_active" /> Actif
          </label>
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
              <th class="px-4 py-2">Prix</th>
              <th class="px-4 py-2">Statut</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @for (service of services(); track service.id) {
              <tr class="border-t border-slate-100">
                <td class="px-4 py-2">{{ service.name }}</td>
                <td class="px-4 py-2">{{ service.price | number }} FCFA</td>
                <td class="px-4 py-2">
                  <button
                    type="button"
                    class="text-xs font-semibold px-2 py-1 rounded-full"
                    [class.bg-green-100]="service.is_active"
                    [class.text-green-800]="service.is_active"
                    [class.bg-slate-100]="!service.is_active"
                    [class.text-slate-600]="!service.is_active"
                    (click)="toggle(service)"
                  >
                    {{ service.is_active ? 'Actif' : 'Inactif' }}
                  </button>
                </td>
                <td class="px-4 py-2 text-right space-x-2">
                  <button type="button" class="text-brand-700 hover:underline" (click)="openEdit(service)">Modifier</button>
                  <button type="button" class="text-red-600 hover:underline" (click)="remove(service)">Supprimer</button>
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
export class AdminServices implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(AdminServiceService);
  private readonly toast = inject(ToastService);

  protected readonly services = signal<Service[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);
  protected readonly formOpen = signal(false);
  protected readonly editingId = signal<number | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    description: [''],
    price: [0, [Validators.required, Validators.min(0)]],
    is_active: [true],
  });

  ngOnInit(): void {
    this.load(1);
  }

  protected load(page: number): void {
    this.service.list(page).subscribe((res) => {
      this.services.set(res.data);
      this.currentPage.set(res.meta.current_page);
      this.lastPage.set(res.meta.last_page);
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset({ name: '', description: '', price: 0, is_active: true });
    this.formOpen.set(true);
  }

  protected openEdit(service: Service): void {
    this.editingId.set(service.id);
    this.form.patchValue({
      name: service.name,
      description: service.description ?? '',
      price: service.price,
      is_active: service.is_active,
    });
    this.formOpen.set(true);
  }

  protected save(): void {
    if (this.form.invalid) return;
    const value = this.form.getRawValue();
    const id = this.editingId();
    const request = id ? this.service.update(id, value) : this.service.create(value);

    request.subscribe({
      next: () => {
        this.toast.success('Service enregistré.');
        this.formOpen.set(false);
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Erreur.'),
    });
  }

  protected toggle(service: Service): void {
    this.service.toggleStatus(service.id).subscribe((res) => {
      this.services.update((list) => list.map((s) => (s.id === service.id ? res.data : s)));
    });
  }

  protected remove(service: Service): void {
    if (!confirm(`Supprimer le service "${service.name}" ?`)) return;
    this.service.delete(service.id).subscribe({
      next: () => {
        this.toast.success('Service supprimé.');
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Suppression impossible.'),
    });
  }
}

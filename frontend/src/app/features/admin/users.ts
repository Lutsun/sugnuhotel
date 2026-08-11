import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { AdminUserService } from '../../core/services/admin/user.service';
import { AuthService } from '../../core/services/auth.service';
import { User } from '../../core/models/user.model';
import { Pagination } from '../../shared/ui/pagination';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-admin-users',
  imports: [ReactiveFormsModule, FormsModule, Pagination],
  template: `
    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Utilisateurs</h1>
        <button type="button" class="bg-brand-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-brand-800" (click)="openCreate()">
          + Nouvel utilisateur
        </button>
      </div>

      <div class="flex gap-3 mb-4">
        <input
          type="text"
          placeholder="Rechercher (nom, email, téléphone)"
          class="border rounded-md px-3 py-2 text-sm flex-1"
          [(ngModel)]="search"
          [ngModelOptions]="{ standalone: true }"
          (ngModelChange)="load(1)"
        />
        <select class="border rounded-md px-3 py-2 text-sm" [(ngModel)]="roleFilter" [ngModelOptions]="{ standalone: true }" (ngModelChange)="load(1)">
          <option value="">Tous les rôles</option>
          <option value="client">Client</option>
          <option value="receptionist">Réceptionniste</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      @if (formOpen()) {
        <form [formGroup]="form" (ngSubmit)="save()" class="bg-white border border-slate-200 rounded-lg p-6 mb-6 space-y-3">
          <h2 class="font-semibold text-slate-900">{{ editingId() ? 'Modifier' : 'Créer' }} un utilisateur</h2>
          <input type="text" placeholder="Nom" formControlName="name" class="w-full border rounded-md px-3 py-2 text-sm" />
          <input type="email" placeholder="Email" formControlName="email" class="w-full border rounded-md px-3 py-2 text-sm" />
          <select formControlName="role" class="w-full border rounded-md px-3 py-2 text-sm">
            <option value="client">Client</option>
            <option value="receptionist">Réceptionniste</option>
            <option value="admin">Admin</option>
          </select>
          <input type="text" placeholder="Téléphone" formControlName="phone" class="w-full border rounded-md px-3 py-2 text-sm" />
          <textarea placeholder="Adresse" formControlName="address" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
          <input
            type="password"
            [placeholder]="editingId() ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'"
            formControlName="password"
            class="w-full border rounded-md px-3 py-2 text-sm"
          />
          <input type="password" placeholder="Confirmer le mot de passe" formControlName="password_confirmation" class="w-full border rounded-md px-3 py-2 text-sm" />
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
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Rôle</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @for (user of users(); track user.id) {
              <tr class="border-t border-slate-100">
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.email }}</td>
                <td class="px-4 py-2">{{ user.role }}</td>
                <td class="px-4 py-2 text-right space-x-2">
                  <button type="button" class="text-brand-700 hover:underline" (click)="openEdit(user)">Modifier</button>
                  @if (user.id !== auth.currentUser()?.id) {
                    <button type="button" class="text-red-600 hover:underline" (click)="remove(user)">Supprimer</button>
                  }
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
export class AdminUsers implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(AdminUserService);
  private readonly toast = inject(ToastService);
  protected readonly auth = inject(AuthService);

  protected readonly users = signal<User[]>([]);
  protected readonly currentPage = signal(1);
  protected readonly lastPage = signal(1);
  protected readonly formOpen = signal(false);
  protected readonly editingId = signal<number | null>(null);
  protected search = '';
  protected roleFilter = '';

  protected readonly form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    role: ['client', Validators.required],
    phone: [''],
    address: [''],
    password: [''],
    password_confirmation: [''],
  });

  ngOnInit(): void {
    this.load(1);
  }

  protected load(page: number): void {
    this.service.list({ role: this.roleFilter || undefined, search: this.search || undefined, page }).subscribe((res) => {
      this.users.set(res.data);
      this.currentPage.set(res.meta.current_page);
      this.lastPage.set(res.meta.last_page);
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset({ name: '', email: '', role: 'client', phone: '', address: '', password: '', password_confirmation: '' });
    this.formOpen.set(true);
  }

  protected openEdit(user: User): void {
    this.editingId.set(user.id);
    this.form.patchValue({
      name: user.name,
      email: user.email,
      role: user.role,
      phone: user.phone ?? '',
      address: user.address ?? '',
      password: '',
      password_confirmation: '',
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
        this.toast.success('Utilisateur enregistré.');
        this.formOpen.set(false);
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(this.firstError(err) ?? 'Erreur.'),
    });
  }

  protected remove(user: User): void {
    if (!confirm(`Supprimer l'utilisateur "${user.name}" ?`)) return;
    this.service.delete(user.id).subscribe({
      next: () => {
        this.toast.success('Utilisateur supprimé.');
        this.load(this.currentPage());
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Suppression impossible.'),
    });
  }

  private firstError(err: any): string | null {
    const errors = err.error?.errors;
    if (!errors) return err.error?.message ?? null;
    const firstKey = Object.keys(errors)[0];
    return errors[firstKey]?.[0] ?? null;
  }
}

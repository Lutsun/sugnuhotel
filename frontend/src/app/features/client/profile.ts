import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { ProfileService } from '../../core/services/profile.service';
import { ToastService } from '../../shared/toast/toast.service';

@Component({
  selector: 'app-profile',
  imports: [ReactiveFormsModule, FormsModule],
  template: `
    <div class="max-w-2xl mx-auto px-4 py-10 space-y-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-6">Mon profil</h1>

        <form [formGroup]="form" (ngSubmit)="save()" class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nom complet</label>
            <input type="text" formControlName="name" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" formControlName="email" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
            <input type="text" formControlName="phone" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Adresse</label>
            <textarea formControlName="address" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe (optionnel)</label>
            <input type="password" formControlName="password" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmer le mot de passe</label>
            <input type="password" formControlName="password_confirmation" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>

          <button
            type="submit"
            class="bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-md hover:bg-brand-800 disabled:opacity-50"
            [disabled]="saving()"
          >
            Enregistrer
          </button>
        </form>
      </div>

      <div class="bg-white border border-red-200 rounded-lg p-6">
        <h2 class="font-semibold text-red-700 mb-2">Supprimer mon compte</h2>
        <p class="text-sm text-slate-500 mb-3">Cette action est irréversible.</p>
        <div class="flex gap-2">
          <input
            type="password"
            [(ngModel)]="deletePassword"
            [ngModelOptions]="{ standalone: true }"
            placeholder="Mot de passe"
            class="border rounded-md px-3 py-2 text-sm flex-1"
          />
          <button
            type="button"
            class="bg-red-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-red-700"
            (click)="deleteAccount()"
          >
            Supprimer
          </button>
        </div>
      </div>
    </div>
  `,
})
export class Profile implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly profileService = inject(ProfileService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);
  private readonly router = inject(Router);

  protected readonly saving = signal(false);
  protected deletePassword = '';

  protected readonly form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    phone: [''],
    address: [''],
    password: [''],
    password_confirmation: [''],
  });

  ngOnInit(): void {
    this.profileService.show().subscribe((res) => {
      this.form.patchValue({
        name: res.data.name,
        email: res.data.email,
        phone: res.data.phone ?? '',
        address: res.data.address ?? '',
      });
    });
  }

  protected save(): void {
    if (this.form.invalid) return;
    this.saving.set(true);
    const value = this.form.getRawValue();
    const payload = {
      name: value.name,
      email: value.email,
      phone: value.phone || undefined,
      address: value.address || undefined,
      ...(value.password ? { password: value.password, password_confirmation: value.password_confirmation } : {}),
    };

    this.profileService.update(payload).subscribe({
      next: (res) => {
        this.saving.set(false);
        this.auth.setUser(res.data);
        this.toast.success('Profil mis à jour.');
        this.form.patchValue({ password: '', password_confirmation: '' });
      },
      error: (err) => {
        this.saving.set(false);
        this.toast.error(err.error?.message ?? 'Impossible de mettre à jour le profil.');
      },
    });
  }

  protected deleteAccount(): void {
    if (!this.deletePassword) {
      this.toast.error('Veuillez saisir votre mot de passe.');
      return;
    }
    this.profileService.destroy(this.deletePassword).subscribe({
      next: () => {
        localStorage.clear();
        this.router.navigateByUrl('/');
      },
      error: (err) => this.toast.error(err.error?.message ?? 'Mot de passe incorrect.'),
    });
  }
}

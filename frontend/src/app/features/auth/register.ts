import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-register',
  imports: [ReactiveFormsModule, RouterLink],
  template: `
    <div class="max-w-md mx-auto px-4 py-16">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Créer un compte</h1>

      <form [formGroup]="form" (ngSubmit)="submit()" class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
        @if (error()) {
          <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">{{ error() }}</p>
        }

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
          <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
          <input type="password" formControlName="password" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Confirmer le mot de passe</label>
          <input type="password" formControlName="password_confirmation" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>

        <button
          type="submit"
          class="w-full bg-brand-700 text-white font-semibold py-2.5 rounded-md hover:bg-brand-800 disabled:opacity-50"
          [disabled]="form.invalid || loading()"
        >
          Créer mon compte
        </button>

        <p class="text-sm text-center">
          Déjà inscrit ? <a routerLink="/login" class="text-brand-700 hover:underline">Se connecter</a>
        </p>
      </form>
    </div>
  `,
})
export class Register {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);

  protected readonly loading = signal(false);
  protected readonly error = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    phone: [''],
    password: ['', [Validators.required, Validators.minLength(8)]],
    password_confirmation: ['', Validators.required],
  });

  protected submit(): void {
    if (this.form.invalid) return;
    this.loading.set(true);
    this.error.set(null);

    this.auth.register(this.form.getRawValue()).subscribe({
      next: () => this.auth.redirectAfterLogin(),
      error: (err) => {
        this.loading.set(false);
        this.error.set(this.firstError(err) ?? "Impossible de créer le compte.");
      },
    });
  }

  private firstError(err: any): string | null {
    const errors = err.error?.errors;
    if (!errors) return err.error?.message ?? null;
    const firstKey = Object.keys(errors)[0];
    return errors[firstKey]?.[0] ?? null;
  }
}

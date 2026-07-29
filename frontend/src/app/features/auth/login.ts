import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-login',
  imports: [ReactiveFormsModule, RouterLink],
  template: `
    <div class="max-w-md mx-auto px-4 py-16">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Connexion</h1>

      <form [formGroup]="form" (ngSubmit)="submit()" class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
        @if (error()) {
          <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">{{ error() }}</p>
        }

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input type="email" formControlName="email" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
          <input type="password" formControlName="password" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>

        <button
          type="submit"
          class="w-full bg-brand-700 text-white font-semibold py-2.5 rounded-md hover:bg-brand-800 disabled:opacity-50"
          [disabled]="form.invalid || loading()"
        >
          Se connecter
        </button>

        <div class="flex justify-between text-sm">
          <a routerLink="/forgot-password" class="text-brand-700 hover:underline">Mot de passe oublié ?</a>
          <a routerLink="/register" class="text-brand-700 hover:underline">Créer un compte</a>
        </div>
      </form>
    </div>
  `,
})
export class Login {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);

  protected readonly loading = signal(false);
  protected readonly error = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', Validators.required],
  });

  protected submit(): void {
    if (this.form.invalid) return;
    this.loading.set(true);
    this.error.set(null);

    const { email, password } = this.form.getRawValue();
    this.auth.login(email, password).subscribe({
      next: () => this.auth.redirectAfterLogin(),
      error: (err) => {
        this.loading.set(false);
        this.error.set(err.error?.message ?? 'Identifiants incorrects.');
      },
    });
  }
}

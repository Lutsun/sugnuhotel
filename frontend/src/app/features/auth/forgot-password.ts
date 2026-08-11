import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-forgot-password',
  imports: [ReactiveFormsModule, RouterLink],
  template: `
    <section class="min-h-[calc(100vh-64px-1px)] flex items-center justify-center px-4 py-16">
      <div class="w-full max-w-sm">
        <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Réinitialisation</span>
        <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2 mb-2">Mot de passe oublié</h1>
        <p class="text-sm text-ink-400 mb-8">Indiquez votre email, nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>

        <form [formGroup]="form" (ngSubmit)="submit()" class="space-y-4">
          @if (message()) {
            <p class="flex items-start gap-2 text-sm text-green-700 bg-green-50 border border-green-100 rounded-xl px-3 py-2.5">
              <svg class="w-4 h-4 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12.5 2.5 2.5 4.5-5" /></svg>
              {{ message() }}
            </p>
          }
          @if (error()) {
            <p class="flex items-start gap-2 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl px-3 py-2.5">
              <svg class="w-4 h-4 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" d="M12 8v5M12 16h.01" /></svg>
              {{ error() }}
            </p>
          }

          <div>
            <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1.5">Email</label>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6.5A1.5 1.5 0 0 1 4.5 5h15A1.5 1.5 0 0 1 21 6.5v11a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 17.5v-11Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m4 6.5 8 6.5 8-6.5" />
              </svg>
              <input
                type="email"
                formControlName="email"
                autocomplete="email"
                placeholder="vous@exemple.com"
                class="w-full border border-ink-100 rounded-xl pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition"
              />
            </div>
          </div>

          <button
            type="submit"
            class="w-full bg-brand-600 text-white font-semibold py-3 rounded-full hover:bg-brand-700 transition shadow-lg shadow-brand-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            [disabled]="form.invalid || loading()"
          >
            @if (loading()) {
              <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" stroke-opacity=".25" /><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
            }
            Envoyer le lien de réinitialisation
          </button>

          <p class="text-sm text-center text-ink-400">
            <a routerLink="/login" class="text-brand-700 font-medium hover:underline">Retour à la connexion</a>
          </p>
        </form>
      </div>
    </section>
  `,
})
export class ForgotPassword {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);

  protected readonly loading = signal(false);
  protected readonly message = signal<string | null>(null);
  protected readonly error = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
  });

  protected submit(): void {
    if (this.form.invalid) return;
    this.loading.set(true);
    this.message.set(null);
    this.error.set(null);

    this.auth.forgotPassword(this.form.getRawValue().email).subscribe({
      next: (res) => {
        this.loading.set(false);
        this.message.set(res.message ?? 'Un email vous a été envoyé.');
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err.error?.errors?.email?.[0] ?? "Impossible d'envoyer le lien.");
      },
    });
  }
}

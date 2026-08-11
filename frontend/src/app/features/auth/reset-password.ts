import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-reset-password',
  imports: [ReactiveFormsModule],
  template: `
    <section class="min-h-[calc(100vh-64px-1px)] flex items-center justify-center px-4 py-16">
      <div class="w-full max-w-sm">
        <span class="text-brand-600 text-xs font-semibold tracking-[0.2em] uppercase">Dernière étape</span>
        <h1 class="font-display text-3xl font-semibold text-ink-900 mt-2 mb-8">Nouveau mot de passe</h1>

        <form [formGroup]="form" (ngSubmit)="submit()" class="space-y-4">
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
                class="w-full border border-ink-100 rounded-xl pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1.5">Nouveau mot de passe</label>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="4" y="10.5" width="16" height="9.5" rx="2" />
                <path stroke-linecap="round" d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5" />
              </svg>
              <input
                [type]="showPassword() ? 'text' : 'password'"
                formControlName="password"
                autocomplete="new-password"
                class="w-full border border-ink-100 rounded-xl pl-10 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition"
              />
              <button
                type="button"
                (click)="showPassword.set(!showPassword())"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-700 transition"
                [attr.aria-label]="showPassword() ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
              >
                @if (showPassword()) {
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" /><path stroke-linecap="round" stroke-linejoin="round" d="M10.6 5.1A10.7 10.7 0 0 1 12 5c5 0 9 4 10 7-.4 1.1-1.2 2.4-2.3 3.6M6.2 6.2C4.2 7.5 2.8 9.4 2 12c1 3 5 7 10 7 1.4 0 2.7-.3 3.9-.8" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 9.9a3 3 0 0 0 4.2 4.2" /></svg>
                } @else {
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12c1-3 5-7 10-7s9 4 10 7c-1 3-5 7-10 7s-9-4-10-7Z" /><circle cx="12" cy="12" r="3" /></svg>
                }
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-ink-400 uppercase tracking-wide mb-1.5">Confirmer le mot de passe</label>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="4" y="10.5" width="16" height="9.5" rx="2" />
                <path stroke-linecap="round" d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5" />
              </svg>
              <input
                [type]="showPassword() ? 'text' : 'password'"
                formControlName="password_confirmation"
                autocomplete="new-password"
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
            Réinitialiser le mot de passe
          </button>
        </form>
      </div>
    </section>
  `,
})
export class ResetPassword implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);

  protected readonly loading = signal(false);
  protected readonly error = signal<string | null>(null);
  protected readonly showPassword = signal(false);

  protected readonly form = this.fb.nonNullable.group({
    token: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(8)]],
    password_confirmation: ['', Validators.required],
  });

  ngOnInit(): void {
    const params = this.route.snapshot.queryParamMap;
    this.form.patchValue({
      token: params.get('token') ?? '',
      email: params.get('email') ?? '',
    });
  }

  protected submit(): void {
    if (this.form.invalid) return;
    this.loading.set(true);
    this.error.set(null);

    this.auth.resetPassword(this.form.getRawValue()).subscribe({
      next: () => this.router.navigateByUrl('/login'),
      error: (err) => {
        this.loading.set(false);
        this.error.set(err.error?.errors?.email?.[0] ?? 'Lien invalide ou expiré.');
      },
    });
  }
}

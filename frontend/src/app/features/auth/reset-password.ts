import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-reset-password',
  imports: [ReactiveFormsModule],
  template: `
    <div class="max-w-md mx-auto px-4 py-16">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Réinitialiser le mot de passe</h1>

      <form [formGroup]="form" (ngSubmit)="submit()" class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
        @if (error()) {
          <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">{{ error() }}</p>
        }

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input type="email" formControlName="email" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
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
          Réinitialiser
        </button>
      </form>
    </div>
  `,
})
export class ResetPassword implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);

  protected readonly loading = signal(false);
  protected readonly error = signal<string | null>(null);

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

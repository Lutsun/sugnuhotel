import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-forgot-password',
  imports: [ReactiveFormsModule],
  template: `
    <div class="max-w-md mx-auto px-4 py-16">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Mot de passe oublié</h1>

      <form [formGroup]="form" (ngSubmit)="submit()" class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
        @if (message()) {
          <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-md px-3 py-2">{{ message() }}</p>
        }
        @if (error()) {
          <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">{{ error() }}</p>
        }

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input type="email" formControlName="email" class="w-full border rounded-md px-3 py-2 text-sm" />
        </div>

        <button
          type="submit"
          class="w-full bg-brand-700 text-white font-semibold py-2.5 rounded-md hover:bg-brand-800 disabled:opacity-50"
          [disabled]="form.invalid || loading()"
        >
          Envoyer le lien de réinitialisation
        </button>
      </form>
    </div>
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

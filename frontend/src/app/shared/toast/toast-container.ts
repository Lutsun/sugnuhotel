import { Component, inject } from '@angular/core';
import { ToastService } from './toast.service';

@Component({
  selector: 'app-toast-container',
  template: `
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-80">
      @for (toast of toastService.toasts(); track toast.id) {
        <div
          class="rounded-lg px-4 py-3 shadow-lg text-sm text-white flex items-start justify-between gap-3"
          [class.bg-green-600]="toast.type === 'success'"
          [class.bg-red-600]="toast.type === 'error'"
          [class.bg-slate-700]="toast.type === 'info'"
        >
          <span>{{ toast.message }}</span>
          <button type="button" class="opacity-80 hover:opacity-100" (click)="toastService.dismiss(toast.id)">✕</button>
        </div>
      }
    </div>
  `,
})
export class ToastContainer {
  protected readonly toastService = inject(ToastService);
}

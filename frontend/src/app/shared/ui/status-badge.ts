import { Component, Input } from '@angular/core';

const LABELS: Record<string, string> = {
  pending: 'En attente',
  confirmed: 'Confirmée',
  checked_in: 'Enregistrée',
  checked_out: 'Terminée',
  cancelled: 'Annulée',
};

const CLASSES: Record<string, string> = {
  pending: 'bg-amber-100 text-amber-800',
  confirmed: 'bg-green-100 text-green-800',
  checked_in: 'bg-blue-100 text-blue-800',
  checked_out: 'bg-slate-100 text-slate-700',
  cancelled: 'bg-red-100 text-red-800',
};

@Component({
  selector: 'app-status-badge',
  template: `
    <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap" [class]="classes">{{ label }}</span>
  `,
})
export class StatusBadge {
  @Input() status = '';

  protected get label(): string {
    return LABELS[this.status] ?? this.status;
  }

  protected get classes(): string {
    return CLASSES[this.status] ?? 'bg-ink-100 text-ink-700';
  }
}

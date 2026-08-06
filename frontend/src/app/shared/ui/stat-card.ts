import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-stat-card',
  template: `
    <div class="bg-white border border-ink-100 rounded-xl p-5 flex items-start gap-4 hover:shadow-md transition">
      <div class="w-11 h-11 shrink-0 rounded-full flex items-center justify-center" [class]="iconClass">
        <ng-content />
      </div>
      <div class="min-w-0">
        <p class="text-xs font-medium text-ink-400 uppercase tracking-wide">{{ label }}</p>
        <p class="text-2xl font-display font-semibold text-ink-900 mt-0.5">{{ value }}</p>
        @if (sublabel) {
          <p class="text-xs text-ink-400 mt-1">{{ sublabel }}</p>
        }
      </div>
    </div>
  `,
})
export class StatCard {
  @Input() label = '';
  @Input() value: string | number = '';
  @Input() sublabel?: string;
  @Input() iconClass = 'bg-brand-50 text-brand-700';
}

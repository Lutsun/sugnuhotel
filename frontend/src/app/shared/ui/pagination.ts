import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-pagination',
  template: `
    @if (lastPage > 1) {
      <div class="flex items-center justify-center gap-2 mt-6">
        <button
          type="button"
          class="px-3 py-1.5 rounded-md border text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-brand-50"
          [disabled]="currentPage <= 1"
          (click)="pageChange.emit(currentPage - 1)"
        >
          ← Précédent
        </button>
        <span class="text-sm text-slate-600">Page {{ currentPage }} / {{ lastPage }}</span>
        <button
          type="button"
          class="px-3 py-1.5 rounded-md border text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-brand-50"
          [disabled]="currentPage >= lastPage"
          (click)="pageChange.emit(currentPage + 1)"
        >
          Suivant →
        </button>
      </div>
    }
  `,
})
export class Pagination {
  @Input() currentPage = 1;
  @Input() lastPage = 1;
  @Output() pageChange = new EventEmitter<number>();
}

import { FullCalendarModule } from '@fullcalendar/angular';
import dayGridPlugin from '@fullcalendar/daygrid';
import { CalendarOptions } from '@fullcalendar/core';
import { Component, OnInit, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { ReceptionDashboardService } from '../../core/services/reception/dashboard.service';

@Component({
  selector: 'app-reception-calendar',
  imports: [FullCalendarModule],
  template: `
    <div class="max-w-6xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-slate-900 mb-6">Calendrier des réservations</h1>
      <div class="bg-white border border-slate-200 rounded-lg p-4">
        <full-calendar [options]="options()" />
      </div>
    </div>
  `,
})
export class ReceptionCalendar implements OnInit {
  private readonly service = inject(ReceptionDashboardService);
  private readonly router = inject(Router);

  protected readonly options = signal<CalendarOptions>({
    plugins: [dayGridPlugin],
    initialView: 'dayGridMonth',
    height: 'auto',
    events: [],
    eventClick: (info) => {
      this.router.navigate(['/reception/reservations', info.event.id]);
    },
  });

  ngOnInit(): void {
    this.service.calendar().subscribe((events) => {
      this.options.update((opts) => ({
        ...opts,
        events: events.map((e) => ({ ...e, id: String(e.id) })),
      }));
    });
  }
}

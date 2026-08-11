import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { Reservation } from '../../models/reservation.model';

export interface ReceptionDashboard {
  rooms: { total: number; available: number; occupied: number };
  today_arrivals: Reservation[];
  today_departures: Reservation[];
  current_guests: Reservation[];
  upcoming_arrivals: Reservation[];
}

export interface CalendarEvent {
  id: number;
  title: string;
  start: string;
  end: string;
  backgroundColor: string;
  borderColor: string;
}

@Injectable({ providedIn: 'root' })
export class ReceptionDashboardService {
  constructor(private readonly http: HttpClient) {}

  get(): Observable<ReceptionDashboard> {
    return this.http.get<ReceptionDashboard>(`${environment.apiUrl}/reception/dashboard`);
  }

  calendar(): Observable<CalendarEvent[]> {
    return this.http.get<CalendarEvent[]>(`${environment.apiUrl}/reception/calendar`);
  }
}

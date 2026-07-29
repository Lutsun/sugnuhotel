import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { Reservation } from '../../models/reservation.model';

export interface AdminDashboard {
  rooms: { total: number; available: number; occupied: number; maintenance: number; occupancy_rate: number };
  today: { arrivals: number; departures: number };
  reservations: { total: number; pending: number; confirmed: number; cancelled: number };
  revenue: { monthly: number; total: number };
  users: { total: number; new_this_month: number };
  recent_reservations: Reservation[];
}

@Injectable({ providedIn: 'root' })
export class AdminDashboardService {
  constructor(private readonly http: HttpClient) {}

  get(): Observable<AdminDashboard> {
    return this.http.get<AdminDashboard>(`${environment.apiUrl}/admin/dashboard`);
  }
}

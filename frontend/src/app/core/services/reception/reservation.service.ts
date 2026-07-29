import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { LaravelPaginated } from '../../models/pagination.model';
import { Reservation } from '../../models/reservation.model';

export interface CreateOptions {
  available_rooms: { id: number; room_number: string; room_type: string; price_per_night: number; max_occupancy: number }[];
  clients: { id: number; name: string; email: string }[];
  services: { id: number; name: string; price: number }[];
}

export interface ReceptionCreatePayload {
  user_id: number;
  room_id: number;
  check_in_date: string;
  check_out_date: string;
  number_of_adults: number;
  number_of_children?: number;
  special_requests?: string;
  services?: number[];
}

export interface ReceptionUpdatePayload {
  room_id?: number;
  check_in_date?: string;
  check_out_date?: string;
  number_of_adults?: number;
  number_of_children?: number;
  special_requests?: string;
  status?: string;
}

@Injectable({ providedIn: 'root' })
export class ReceptionReservationService {
  private readonly base = `${environment.apiUrl}/reception/reservations`;

  constructor(private readonly http: HttpClient) {}

  list(params: { status?: string; date?: string; search?: string; page?: number } = {}): Observable<LaravelPaginated<Reservation>> {
    const cleaned: Record<string, string> = {};
    if (params.status) cleaned['status'] = params.status;
    if (params.date) cleaned['date'] = params.date;
    if (params.search) cleaned['search'] = params.search;
    if (params.page) cleaned['page'] = String(params.page);
    return this.http.get<LaravelPaginated<Reservation>>(this.base, { params: cleaned });
  }

  search(query: string): Observable<{ data: Reservation[] }> {
    return this.http.get<{ data: Reservation[] }>(`${this.base}/search`, { params: { q: query } });
  }

  createOptions(): Observable<CreateOptions> {
    return this.http.get<CreateOptions>(`${this.base}/create-options`);
  }

  show(id: number): Observable<{ data: Reservation }> {
    return this.http.get<{ data: Reservation }>(`${this.base}/${id}`);
  }

  create(payload: ReceptionCreatePayload): Observable<{ data: Reservation }> {
    return this.http.post<{ data: Reservation }>(this.base, payload);
  }

  update(id: number, payload: ReceptionUpdatePayload): Observable<{ data: Reservation }> {
    return this.http.patch<{ data: Reservation }>(`${this.base}/${id}`, payload);
  }

  checkIn(id: number): Observable<{ data: Reservation }> {
    return this.http.post<{ data: Reservation }>(`${this.base}/${id}/checkin`, {});
  }

  checkOut(id: number): Observable<{ data: Reservation }> {
    return this.http.post<{ data: Reservation }>(`${this.base}/${id}/checkout`, {});
  }

  cancel(id: number): Observable<{ data: Reservation }> {
    return this.http.post<{ data: Reservation }>(`${this.base}/${id}/cancel`, {});
  }
}

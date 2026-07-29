import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { LaravelPaginated } from '../models/pagination.model';
import { Room } from '../models/room.model';
import { Service } from '../models/service.model';
import { Reservation } from '../models/reservation.model';

export interface SearchMeta {
  check_in: string;
  check_out: string;
  nights: number;
  adults: number;
  children: number;
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface SearchResult {
  rooms: Room[];
  meta: SearchMeta;
}

export interface ConfirmDetails {
  room: Room;
  services: Service[];
  nights: number;
  room_price: number;
  check_in: string;
  check_out: string;
  adults: number;
  children: number;
}

export interface CreateBookingPayload {
  room_id: number;
  check_in: string;
  check_out: string;
  adults: number;
  children?: number;
  special_requests?: string;
  services?: number[];
}

@Injectable({ providedIn: 'root' })
export class BookingService {
  constructor(private readonly http: HttpClient) {}

  search(params: {
    check_in: string;
    check_out: string;
    adults: number;
    children?: number;
    min_price?: number;
    max_price?: number;
    room_types?: number[];
    sort?: string;
    page?: number;
  }): Observable<SearchResult> {
    let httpParams: Record<string, string> = {
      check_in: params.check_in,
      check_out: params.check_out,
      adults: String(params.adults),
    };
    if (params.children) httpParams['children'] = String(params.children);
    if (params.min_price) httpParams['min_price'] = String(params.min_price);
    if (params.max_price) httpParams['max_price'] = String(params.max_price);
    if (params.sort) httpParams['sort'] = params.sort;
    if (params.page) httpParams['page'] = String(params.page);

    let url = `${environment.apiUrl}/booking/search`;
    const query = new URLSearchParams(httpParams);
    (params.room_types ?? []).forEach((id) => query.append('room_types[]', String(id)));

    return this.http.get<SearchResult>(`${url}?${query.toString()}`);
  }

  checkAvailability(roomId: number, checkIn: string, checkOut: string): Observable<{ available: boolean; message: string }> {
    return this.http.get<{ available: boolean; message: string }>(
      `${environment.apiUrl}/booking/rooms/${roomId}/availability`,
      { params: { check_in: checkIn, check_out: checkOut } },
    );
  }

  confirmDetails(roomId: number, checkIn: string, checkOut: string, adults: number, children: number): Observable<ConfirmDetails> {
    return this.http.get<ConfirmDetails>(`${environment.apiUrl}/booking/rooms/${roomId}/confirm`, {
      params: { check_in: checkIn, check_out: checkOut, adults: String(adults), children: String(children) },
    });
  }

  create(payload: CreateBookingPayload): Observable<{ data: Reservation }> {
    return this.http.post<{ data: Reservation }>(`${environment.apiUrl}/bookings`, payload);
  }

  myReservations(page = 1): Observable<LaravelPaginated<Reservation>> {
    return this.http.get<LaravelPaginated<Reservation>>(`${environment.apiUrl}/bookings`, {
      params: { page: String(page) },
    });
  }

  show(id: number): Observable<{ data: Reservation }> {
    return this.http.get<{ data: Reservation }>(`${environment.apiUrl}/bookings/${id}`);
  }

  cancel(id: number): Observable<{ data: Reservation }> {
    return this.http.post<{ data: Reservation }>(`${environment.apiUrl}/bookings/${id}/cancel`, {});
  }
}

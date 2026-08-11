import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { LaravelPaginated } from '../models/pagination.model';
import { Room, RoomType } from '../models/room.model';
import { Service } from '../models/service.model';

export interface HomeData {
  room_types: RoomType[];
  services: Service[];
  rooms: Room[];
}

@Injectable({ providedIn: 'root' })
export class RoomService {
  constructor(private readonly http: HttpClient) {}

  home(): Observable<HomeData> {
    return this.http.get<HomeData>(`${environment.apiUrl}/home`);
  }

  list(params: Record<string, string | number | undefined> = {}): Observable<LaravelPaginated<Room>> {
    return this.http.get<LaravelPaginated<Room>>(`${environment.apiUrl}/rooms`, {
      params: this.cleanParams(params),
    });
  }

  show(id: number): Observable<{ room: Room; similar_rooms: Room[] }> {
    return this.http.get<{ room: Room; similar_rooms: Room[] }>(`${environment.apiUrl}/rooms/${id}`);
  }

  roomTypes(): Observable<{ data: RoomType[] }> {
    return this.http.get<{ data: RoomType[] }>(`${environment.apiUrl}/room-types`);
  }

  activeServices(): Observable<{ data: Service[] }> {
    return this.http.get<{ data: Service[] }>(`${environment.apiUrl}/services`);
  }

  private cleanParams(params: Record<string, string | number | undefined>): Record<string, string> {
    const cleaned: Record<string, string> = {};
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null && value !== '') {
        cleaned[key] = String(value);
      }
    }
    return cleaned;
  }
}

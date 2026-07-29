import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { LaravelPaginated } from '../../models/pagination.model';
import { Room, RoomStatus } from '../../models/room.model';

export interface RoomFormPayload {
  room_number: string;
  room_type_id: number;
  floor: number;
  price_per_night: number;
  max_occupancy: number;
  status: RoomStatus;
}

@Injectable({ providedIn: 'root' })
export class AdminRoomService {
  private readonly base = `${environment.apiUrl}/admin/rooms`;

  constructor(private readonly http: HttpClient) {}

  list(params: Record<string, string | number | undefined> = {}): Observable<LaravelPaginated<Room>> {
    const cleaned: Record<string, string> = {};
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== '') cleaned[key] = String(value);
    }
    return this.http.get<LaravelPaginated<Room>>(this.base, { params: cleaned });
  }

  create(payload: FormData): Observable<{ data: Room }> {
    return this.http.post<{ data: Room }>(this.base, payload);
  }

  update(id: number, payload: RoomFormPayload): Observable<{ data: Room }> {
    return this.http.put<{ data: Room }>(`${this.base}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.base}/${id}`);
  }

  uploadImage(roomId: number, file: File): Observable<{ data: Room }> {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post<{ data: Room }>(`${this.base}/${roomId}/images`, formData);
  }

  deleteImage(imageId: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${environment.apiUrl}/admin/rooms/images/${imageId}`);
  }
}

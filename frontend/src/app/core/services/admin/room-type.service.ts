import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { LaravelPaginated } from '../../models/pagination.model';
import { RoomType } from '../../models/room.model';

@Injectable({ providedIn: 'root' })
export class AdminRoomTypeService {
  private readonly base = `${environment.apiUrl}/admin/room-types`;

  constructor(private readonly http: HttpClient) {}

  list(page = 1): Observable<LaravelPaginated<RoomType>> {
    return this.http.get<LaravelPaginated<RoomType>>(this.base, { params: { page: String(page) } });
  }

  create(payload: FormData): Observable<{ data: RoomType }> {
    return this.http.post<{ data: RoomType }>(this.base, payload);
  }

  update(id: number, payload: FormData): Observable<{ data: RoomType }> {
    payload.append('_method', 'PUT');
    return this.http.post<{ data: RoomType }>(`${this.base}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.base}/${id}`);
  }
}

import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { LaravelPaginated } from '../../models/pagination.model';
import { Service } from '../../models/service.model';

@Injectable({ providedIn: 'root' })
export class AdminServiceService {
  private readonly base = `${environment.apiUrl}/admin/services`;

  constructor(private readonly http: HttpClient) {}

  list(page = 1): Observable<LaravelPaginated<Service>> {
    return this.http.get<LaravelPaginated<Service>>(this.base, { params: { page: String(page) } });
  }

  create(payload: Partial<Service>): Observable<{ data: Service }> {
    return this.http.post<{ data: Service }>(this.base, payload);
  }

  update(id: number, payload: Partial<Service>): Observable<{ data: Service }> {
    return this.http.put<{ data: Service }>(`${this.base}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.base}/${id}`);
  }

  toggleStatus(id: number): Observable<{ data: Service }> {
    return this.http.patch<{ data: Service }>(`${this.base}/${id}/toggle-status`, {});
  }
}

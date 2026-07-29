import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { LaravelPaginated } from '../../models/pagination.model';
import { User } from '../../models/user.model';

export interface AdminUserPayload {
  name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  role: string;
  phone?: string;
  address?: string;
}

@Injectable({ providedIn: 'root' })
export class AdminUserService {
  private readonly base = `${environment.apiUrl}/admin/users`;

  constructor(private readonly http: HttpClient) {}

  list(params: { role?: string; search?: string; page?: number } = {}): Observable<LaravelPaginated<User>> {
    const cleaned: Record<string, string> = {};
    if (params.role) cleaned['role'] = params.role;
    if (params.search) cleaned['search'] = params.search;
    if (params.page) cleaned['page'] = String(params.page);
    return this.http.get<LaravelPaginated<User>>(this.base, { params: cleaned });
  }

  create(payload: AdminUserPayload): Observable<{ data: User }> {
    return this.http.post<{ data: User }>(this.base, payload);
  }

  update(id: number, payload: AdminUserPayload): Observable<{ data: User }> {
    return this.http.put<{ data: User }>(`${this.base}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.base}/${id}`);
  }
}

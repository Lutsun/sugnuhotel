import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { User } from '../models/user.model';

export interface UpdateProfilePayload {
  name: string;
  email: string;
  phone?: string;
  address?: string;
  password?: string;
  password_confirmation?: string;
}

@Injectable({ providedIn: 'root' })
export class ProfileService {
  constructor(private readonly http: HttpClient) {}

  show(): Observable<{ data: User }> {
    return this.http.get<{ data: User }>(`${environment.apiUrl}/profile`);
  }

  update(payload: UpdateProfilePayload): Observable<{ data: User }> {
    return this.http.patch<{ data: User }>(`${environment.apiUrl}/profile`, payload);
  }

  destroy(password: string): Observable<{ message: string }> {
    return this.http.request<{ message: string }>('delete', `${environment.apiUrl}/profile`, {
      body: { password },
    });
  }
}

import { HttpClient } from '@angular/common/http';
import { Injectable, computed, signal } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { User } from '../models/user.model';

const TOKEN_KEY = 'sugnuhotel_token';
const USER_KEY = 'sugnuhotel_user';

interface AuthResponse {
  user: User;
  token: string;
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly currentUserSignal = signal<User | null>(this.readStoredUser());

  readonly currentUser = this.currentUserSignal.asReadonly();
  readonly isAuthenticated = computed(() => this.currentUserSignal() !== null);
  readonly role = computed(() => this.currentUserSignal()?.role ?? null);

  constructor(
    private readonly http: HttpClient,
    private readonly router: Router,
  ) {}

  get token(): string | null {
    return localStorage.getItem(TOKEN_KEY);
  }

  register(payload: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    phone?: string;
    address?: string;
  }): Observable<AuthResponse> {
    return this.http
      .post<AuthResponse>(`${environment.apiUrl}/register`, payload)
      .pipe(tap((response) => this.storeSession(response)));
  }

  login(email: string, password: string): Observable<AuthResponse> {
    return this.http
      .post<AuthResponse>(`${environment.apiUrl}/login`, { email, password })
      .pipe(tap((response) => this.storeSession(response)));
  }

  logout(): void {
    this.http.post(`${environment.apiUrl}/logout`, {}).subscribe({
      complete: () => this.clearSession(),
      error: () => this.clearSession(),
    });
  }

  forgotPassword(email: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/forgot-password`, { email });
  }

  resetPassword(payload: {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
  }): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/reset-password`, payload);
  }

  refreshMe(): Observable<{ data: User }> {
    return this.http
      .get<{ data: User }>(`${environment.apiUrl}/me`)
      .pipe(tap((response) => this.setUser(response.data)));
  }

  setUser(user: User): void {
    this.currentUserSignal.set(user);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
  }

  /** Redirection post-connexion, miroir de AuthenticatedSessionController::store côté Laravel. */
  redirectAfterLogin(): void {
    const user = this.currentUserSignal();
    if (user?.role === 'admin') {
      this.router.navigateByUrl('/admin/dashboard');
    } else if (user?.role === 'receptionist') {
      this.router.navigateByUrl('/reception/dashboard');
    } else {
      this.router.navigateByUrl('/');
    }
  }

  private storeSession(response: AuthResponse): void {
    localStorage.setItem(TOKEN_KEY, response.token);
    this.setUser(response.user);
  }

  private clearSession(): void {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    this.currentUserSignal.set(null);
    this.router.navigateByUrl('/login');
  }

  private readStoredUser(): User | null {
    const raw = localStorage.getItem(USER_KEY);
    return raw ? (JSON.parse(raw) as User) : null;
  }
}

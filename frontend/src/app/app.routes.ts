import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { guestGuard } from './core/guards/guest.guard';
import { roleGuard } from './core/guards/role.guard';

export const routes: Routes = [
  { path: '', loadComponent: () => import('./features/public/home').then((m) => m.Home) },
  { path: 'rooms', loadComponent: () => import('./features/public/rooms-list').then((m) => m.RoomsList) },
  { path: 'rooms/:id', loadComponent: () => import('./features/public/room-detail').then((m) => m.RoomDetail) },

  { path: 'login', canActivate: [guestGuard], loadComponent: () => import('./features/auth/login').then((m) => m.Login) },
  { path: 'register', canActivate: [guestGuard], loadComponent: () => import('./features/auth/register').then((m) => m.Register) },
  {
    path: 'forgot-password',
    canActivate: [guestGuard],
    loadComponent: () => import('./features/auth/forgot-password').then((m) => m.ForgotPassword),
  },
  {
    path: 'reset-password',
    canActivate: [guestGuard],
    loadComponent: () => import('./features/auth/reset-password').then((m) => m.ResetPassword),
  },

  {
    path: 'booking/search',
    canActivate: [authGuard],
    loadComponent: () => import('./features/client/booking-search').then((m) => m.BookingSearch),
  },
  {
    path: 'booking/confirm/:room',
    canActivate: [authGuard],
    loadComponent: () => import('./features/client/booking-confirm').then((m) => m.BookingConfirm),
  },
  {
    path: 'my-reservations',
    canActivate: [authGuard],
    loadComponent: () => import('./features/client/my-reservations').then((m) => m.MyReservations),
  },
  {
    path: 'my-reservations/:id',
    canActivate: [authGuard],
    loadComponent: () => import('./features/client/reservation-detail').then((m) => m.ReservationDetail),
  },
  { path: 'profile', canActivate: [authGuard], loadComponent: () => import('./features/client/profile').then((m) => m.Profile) },

  {
    path: 'admin/dashboard',
    canActivate: [roleGuard(['admin'])],
    loadComponent: () => import('./features/admin/dashboard').then((m) => m.AdminDashboardPage),
  },
  {
    path: 'admin/room-types',
    canActivate: [roleGuard(['admin'])],
    loadComponent: () => import('./features/admin/room-types').then((m) => m.AdminRoomTypes),
  },
  {
    path: 'admin/rooms',
    canActivate: [roleGuard(['admin'])],
    loadComponent: () => import('./features/admin/rooms').then((m) => m.AdminRooms),
  },
  {
    path: 'admin/services',
    canActivate: [roleGuard(['admin'])],
    loadComponent: () => import('./features/admin/services').then((m) => m.AdminServices),
  },
  {
    path: 'admin/users',
    canActivate: [roleGuard(['admin'])],
    loadComponent: () => import('./features/admin/users').then((m) => m.AdminUsers),
  },

  {
    path: 'reception/dashboard',
    canActivate: [roleGuard(['admin', 'receptionist'])],
    loadComponent: () => import('./features/reception/dashboard').then((m) => m.ReceptionDashboardPage),
  },
  {
    path: 'reception/calendar',
    canActivate: [roleGuard(['admin', 'receptionist'])],
    loadComponent: () => import('./features/reception/calendar').then((m) => m.ReceptionCalendar),
  },
  {
    path: 'reception/reservations/new',
    canActivate: [roleGuard(['admin', 'receptionist'])],
    loadComponent: () => import('./features/reception/reservation-create').then((m) => m.ReceptionReservationCreate),
  },
  {
    path: 'reception/reservations/:id',
    canActivate: [roleGuard(['admin', 'receptionist'])],
    loadComponent: () => import('./features/reception/reservation-detail').then((m) => m.ReceptionReservationDetail),
  },
  {
    path: 'reception/reservations',
    canActivate: [roleGuard(['admin', 'receptionist'])],
    loadComponent: () => import('./features/reception/reservations-list').then((m) => m.ReceptionReservationsList),
  },

  { path: '**', redirectTo: '' },
];

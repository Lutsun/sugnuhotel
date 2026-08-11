import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { ToastService } from '../../shared/toast/toast.service';

const TOKEN_KEY = 'sugnuhotel_token';
const USER_KEY = 'sugnuhotel_user';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const toast = inject(ToastService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401) {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
        if (!req.url.endsWith('/login')) {
          router.navigateByUrl('/login');
        }
      } else if (error.status === 403) {
        toast.error(error.error?.message ?? 'Accès non autorisé.');
      } else if (error.status === 409 || error.status === 422) {
        // Laissé aux formulaires pour affichage inline ; on notifie quand même si pas de champs détaillés.
        if (!error.error?.errors) {
          toast.error(error.error?.message ?? 'Une erreur est survenue.');
        }
      } else if (error.status >= 500) {
        toast.error('Erreur serveur, veuillez réessayer.');
      }

      return throwError(() => error);
    }),
  );
};

import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

/** Empêche un utilisateur déjà connecté d'accéder aux pages login/register. */
export const guestGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router = inject(Router);

  if (!auth.isAuthenticated()) {
    return true;
  }

  auth.redirectAfterLogin();
  return router.createUrlTree(['/']);
};

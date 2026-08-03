# SugnuHotel

Application de réservation d'hôtel — backend API Laravel (Sanctum) + frontend Angular.

**En ligne :**
- Frontend : https://sugnuhotel.vercel.app
- API : https://backend-production-e47d.up.railway.app/api

## Architecture

```
sugnuhotel/
├── app/                    Backend Laravel — API pure (aucune vue Blade)
│   ├── Http/Controllers/Api/   Contrôleurs API (public, client, admin, réception)
│   ├── Http/Resources/         Sérialisation JSON
│   ├── Models/                 Eloquent (User, Room, RoomType, Reservation, Service...)
│   └── Mail/                   Emails (confirmation, annulation, modification)
├── routes/api.php          Toutes les routes de l'API (auth:sanctum + role)
├── database/                Migrations, seeders, factories
├── tests/Feature/Api/       Tests Pest de l'API
└── frontend/                Application Angular (SPA)
    └── src/app/
        ├── core/            Services HTTP, guards, intercepteurs, modèles TS
        ├── shared/          Layout (header/footer), composants UI réutilisables
        └── features/        Pages par domaine (public, auth, client, admin, réception)
```

## Rôles

- **client** : recherche/réservation de chambres, historique, annulation, profil
- **receptionist** : dashboard, calendrier, check-in/check-out, gestion des réservations
- **admin** : tout ce qui précède + CRUD chambres/types de chambres/services/utilisateurs

Voir [COMPTES-TEST.md](COMPTES-TEST.md) pour des comptes de démonstration.

## Lancer le projet en local

### Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
# configurer DB_* dans .env (MySQL), puis :
php artisan migrate --seed
php artisan serve
```

L'API est servie sur `http://localhost:8000/api`.

### Frontend

```bash
cd frontend
npm install
npm run start
```

L'application est servie sur `http://localhost:4200`.

## Tests

```bash
php artisan test
```

## Stack technique

- **Backend** : Laravel 12, Sanctum (auth par token), MySQL
- **Frontend** : Angular 22 (standalone components, signals), Tailwind CSS v4, FullCalendar

## Déploiement

### Frontend (Vercel)

Le dossier `frontend/vercel.json` est déjà configuré (build, dossier de sortie, réécriture SPA).

1. Avant le build, mettre à jour `frontend/src/environments/environment.prod.ts` avec l'URL réelle de l'API déployée.
2. Sur Vercel : nouveau projet → Root Directory = `frontend` → Framework Preset = Angular (ou "Other", `vercel.json` fait le reste).
3. Déployer. Vercel détecte `npm run build` et sert `dist/frontend/browser`.

### Backend (Railway — pas Vercel)

Vercel n'est pas adapté à une vraie application Laravel (pas de runtime PHP persistant, pas de cron/queue, connexions DB par requête). Ce projet est déployé sur **Railway** :

1. Service `backend` connecté au repo GitHub (branche `main`) → redéploiement automatique à chaque push.
2. Service `MySQL` (add-on Railway) — les identifiants sont référencés dans les variables de `backend` via `${{MySQL.MYSQLHOST}}`, etc.
3. `railway.json` définit :
   - `preDeployCommand` : exécute `php artisan migrate --force` puis `storage:link` avant chaque déploiement (équivalent d'une release phase Heroku)
   - `healthcheckPath: /api/home` — **pas `/up`** : le proxy Caddy auto-généré par Railway pour Laravel ne route pas correctement cette route de santé par défaut de Laravel, `/api/home` fonctionne de manière fiable
4. Variables d'environnement définies manuellement : `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `DB_*`, `FRONTEND_URL` (URL Vercel, pour CORS + liens dans les emails), `MAIL_*`.

⚠️ `MAIL_MAILER=log` en production actuellement : les emails ne partent pas réellement, juste écrits dans les logs. À changer pour un vrai fournisseur (Resend, Brevo, Mailtrap...) si besoin.

⚠️ Le stockage local des images (`storage/app/public`) n'est pas persistant sur Railway (le système de fichiers est recréé à chaque déploiement). Pour des photos de chambres qui survivent aux redéploiements, prévoir un stockage externe (S3 ou équivalent) plus tard si besoin — non nécessaire pour un usage de démonstration/projet académique.

# SugnuHotel

Application de réservation d'hôtel — backend API Laravel (Sanctum) + frontend Angular.

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

### Backend (Railway / Render — pas Vercel)

Vercel n'est pas adapté à une vraie application Laravel (pas de runtime PHP persistant, pas de cron/queue, connexions DB par requête). Utiliser un hébergeur pensé pour PHP :

1. Créer un service à partir du repo (Railway/Render détectent Laravel via `composer.json`).
2. Ajouter une base MySQL (souvent un add-on du même hébergeur).
3. Variables d'environnement à définir : toutes celles de `.env.example`, en particulier :
   - `APP_KEY` (générer avec `php artisan key:generate --show`)
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `DB_*` (fournis par l'add-on MySQL)
   - `FRONTEND_URL` = l'URL Vercel du frontend (nécessaire pour le CORS et les liens dans les emails)
   - `MAIL_*` pour envoyer réellement les emails (confirmation/annulation/modification) — `MAIL_MAILER=log` n'envoie rien, c'est un mode debug local uniquement
4. Après le premier déploiement : `php artisan migrate --seed --force` et `php artisan storage:link` (nécessaire pour que les photos de chambres téléversées par l'admin soient accessibles).

⚠️ Le stockage local des images (`storage/app/public`) n'est généralement pas persistant sur ce type d'hébergeur (le système de fichiers est recréé à chaque déploiement). Pour des photos de chambres qui survivent aux redéploiements en production, prévoir un stockage externe (S3 ou équivalent) plus tard si besoin — non nécessaire pour un usage de démonstration/projet académique.

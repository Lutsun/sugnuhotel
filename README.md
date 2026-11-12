# SugnuHotel

Application de réservation d'hôtel — backend API Laravel (Sanctum) + frontend Angular.

**En ligne :**
- Frontend : https://sugnuhotel.vercel.app
- API : https://sugnuhotel-q0kr.onrender.com/api (offre gratuite : la première requête après une période d'inactivité peut prendre 30 à 60 s)

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

### Backend (Render + TiDB Cloud — pas Vercel)

Vercel n'est pas adapté à une vraie application Laravel (pas de runtime PHP persistant, pas de cron/queue, connexions DB par requête). L'API est déployée sur **Render** (service web Docker, offre gratuite) avec une base MySQL **TiDB Cloud Serverless** :

1. Service web connecté au repo GitHub (branche `main`) ; le `Dockerfile` construit l'image (PHP 8.3 + Apache).
2. `docker/entrypoint.sh` s'exécute à chaque démarrage : migrations, seeders de base (services, types de chambres, chambres), `storage:link`, mise en cache de la config et des routes.
3. Variables d'environnement (voir `render.yaml` pour la liste) : `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `FRONTEND_URL` (URL Vercel, pour CORS + liens dans les emails), `DB_HOST`, `DB_PORT=4000`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt` (TiDB impose le SSL), `LOG_CHANNEL=stderr`, `MAIL_MAILER=brevo`, `BREVO_API_KEY`, `MAIL_FROM_ADDRESS`.
4. Comptes de démonstration : créés uniquement si `SEED_DEMO_USERS=true` (voir `COMPTES-TEST.md`). À désactiver une fois la démonstration terminée.

### Emails (Brevo, via API HTTP — pas SMTP)

Les hébergeurs cloud gratuits (Render, Railway...) bloquent les ports SMTP sortants (25, 465, 587) pour lutter contre le spam. L'envoi passe donc par l'**API HTTP de Brevo** plutôt que par un relais SMTP classique :

- Package `symfony/brevo-mailer` + `symfony/http-client`
- Transport personnalisé enregistré dans `AppServiceProvider::boot()` via `Mail::extend('brevo', ...)`, DSN `brevo+api`
- `config/mail.php` : mailer `brevo` (transport `brevo`) ; `config/services.php` : `services.brevo.key` ← `BREVO_API_KEY`
- L'expéditeur `contact@dasylva.dev` est vérifié dans Brevo par confirmation d'email (pas de DNS nécessaire)
- ⚠️ Si la restriction "Authorised IPs" de Brevo est activée, l'IP sortante de l'hébergeur change (pool dynamique, pas d'IP fixe) — la désactiver entièrement plutôt que d'ajouter des IPs une par une (https://app.brevo.com/security/authorised_ips)

En local, le SMTP classique fonctionne très bien (`MAIL_MAILER=smtp` avec les identifiants SMTP Brevo) puisque rien n'y bloque le port 587 — seule la production a besoin du transport API.

⚠️ Le stockage local des images (`storage/app/public`) n'est pas persistant sur Render (le système de fichiers est recréé à chaque déploiement). Pour des photos de chambres qui survivent aux redéploiements, prévoir un stockage externe (S3 ou équivalent) plus tard si besoin — non nécessaire pour un usage de démonstration/projet académique.

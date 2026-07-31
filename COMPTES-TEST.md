# Comptes de test — SugnuHotel

Ces comptes sont créés automatiquement par le seeder (`database/seeders/AdminUserSeeder.php`)
à chaque `php artisan migrate:fresh --seed`.

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | `admin@sugnuhotel.com` | `admin123` |
| Réceptionniste | `reception@sugnuhotel.com` | `recept123` |
| Client | `client@test.com` | `client123` |
| Client (bonus) | `mamadou.ndiaye@email.com` | `password123` |

## Lancer le projet

Backend (depuis la racine `sugnuhotel/`) :

```bash
php artisan serve
```

Frontend (depuis `sugnuhotel/frontend/`) :

```bash
npm run start
```

Puis ouvrir `http://localhost:4200` et se connecter avec l'un des comptes ci-dessus selon le rôle à tester.

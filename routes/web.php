<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ce backend est une API pure (voir routes/api.php) consommée par le
| frontend Angular (dossier frontend/). Il n'y a plus de vues Blade :
| ce fichier est conservé vide car requis par bootstrap/app.php.
|
*/

Route::get('/', fn () => response()->json(['message' => 'SugnuHotel API — voir /api']));

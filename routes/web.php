<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/room/{id}', [HomeController::class, 'showRoom'])->name('room.show');

// Routes de réservation (client)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/booking/search', [BookingController::class, 'search'])->name('booking.search');
    Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check');
    Route::get('/booking/confirm/{room}', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/my-reservations', [BookingController::class, 'myReservations'])->name('booking.my-reservations');
    Route::get('/reservation/{id}', [BookingController::class, 'show'])->name('reservation.show');
    Route::post('/reservation/{id}/cancel', [BookingController::class, 'cancel'])->name('reservation.cancel');
});

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des types de chambres
    Route::resource('room-types', App\Http\Controllers\Admin\RoomTypeController::class);
    
    // Gestion des chambres
    Route::resource('rooms', App\Http\Controllers\Admin\RoomController::class);
    Route::post('/rooms/{room}/images', [App\Http\Controllers\Admin\RoomController::class, 'uploadImage'])->name('rooms.upload-image');
    Route::delete('/rooms/images/{image}', [App\Http\Controllers\Admin\RoomController::class, 'deleteImage'])->name('rooms.delete-image');
    
    // Gestion des services
    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
    
    // Gestion des utilisateurs
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
});

// Routes Réceptionniste
Route::middleware(['auth', 'role:admin,receptionist'])->prefix('reception')->name('reception.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Reception\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [App\Http\Controllers\Reception\DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/reservations', [App\Http\Controllers\Reception\ReservationController::class, 'index'])->name('reservations');
    Route::get('/reservations/search', [App\Http\Controllers\Reception\ReservationController::class, 'search'])->name('reservations.search');
    Route::post('/reservations/{reservation}/checkin', [App\Http\Controllers\Reception\ReservationController::class, 'checkIn'])->name('reservations.checkin');
    Route::post('/reservations/{reservation}/checkout', [App\Http\Controllers\Reception\ReservationController::class, 'checkOut'])->name('reservations.checkout');
});

// ===== ROUTES DE PROFIL =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
<?php

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Api\Admin\RoomTypeController as AdminRoomTypeController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Reception\DashboardController as ReceptionDashboardController;
use App\Http\Controllers\Api\Reception\ReservationController as ReceptionReservationController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

// ===== Routes publiques =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/home', [HomeController::class, 'index']);
Route::get('/services', [HomeController::class, 'services']);
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::get('/room-types', [RoomController::class, 'roomTypes']);

// ===== Routes authentifiées (tous rôles) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Réservation (client)
    Route::get('/booking/search', [BookingController::class, 'search']);
    Route::get('/booking/rooms/{room}/availability', [BookingController::class, 'checkAvailability']);
    Route::get('/booking/rooms/{room}/confirm', [BookingController::class, 'confirm']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings', [BookingController::class, 'myReservations']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // ===== Admin =====
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        Route::apiResource('room-types', AdminRoomTypeController::class);

        Route::apiResource('rooms', AdminRoomController::class);
        Route::post('/rooms/{room}/images', [AdminRoomController::class, 'uploadImage']);
        Route::delete('/rooms/images/{image}', [AdminRoomController::class, 'deleteImage']);

        Route::apiResource('services', AdminServiceController::class);
        Route::patch('/services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus']);

        Route::apiResource('users', AdminUserController::class);
    });

    // ===== Réception =====
    Route::middleware('role:admin,receptionist')->prefix('reception')->group(function () {
        Route::get('/dashboard', [ReceptionDashboardController::class, 'index']);
        Route::get('/calendar', [ReceptionDashboardController::class, 'calendar']);

        Route::get('/reservations/search', [ReceptionReservationController::class, 'search']);
        Route::get('/reservations/create-options', [ReceptionReservationController::class, 'createFormOptions']);
        Route::get('/reservations', [ReceptionReservationController::class, 'index']);
        Route::post('/reservations', [ReceptionReservationController::class, 'store']);
        Route::get('/reservations/{reservation}', [ReceptionReservationController::class, 'show']);
        Route::patch('/reservations/{reservation}', [ReceptionReservationController::class, 'update']);
        Route::post('/reservations/{reservation}/checkin', [ReceptionReservationController::class, 'checkIn']);
        Route::post('/reservations/{reservation}/checkout', [ReceptionReservationController::class, 'checkOut']);
        Route::post('/reservations/{reservation}/cancel', [ReceptionReservationController::class, 'cancel']);
    });
});

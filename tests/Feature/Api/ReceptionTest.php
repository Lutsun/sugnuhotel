<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('blocks clients from the reception area', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client, 'sanctum')
        ->getJson('/api/reception/dashboard')
        ->assertForbidden();
});

it('lets a receptionist check a guest in then out', function () {
    $receptionist = User::factory()->create(['role' => 'receptionist']);
    $room = Room::factory()->create(['status' => 'occupied']);
    $reservation = Reservation::factory()->create(['room_id' => $room->id, 'status' => 'confirmed']);

    $this->actingAs($receptionist, 'sanctum')
        ->postJson("/api/reception/reservations/{$reservation->id}/checkin")
        ->assertOk()
        ->assertJsonPath('data.status', 'checked_in');

    expect(Room::find($room->id)->status)->toBe('occupied');

    $this->actingAs($receptionist, 'sanctum')
        ->postJson("/api/reception/reservations/{$reservation->id}/checkout")
        ->assertOk()
        ->assertJsonPath('data.status', 'checked_out');

    expect(Room::find($room->id)->status)->toBe('available');
});

it('lets a receptionist modify reservation dates and notifies the guest by email', function () {
    $receptionist = User::factory()->create(['role' => 'receptionist']);
    $room = Room::factory()->create(['price_per_night' => 40000]);
    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'check_in_date' => now()->addDays(5)->format('Y-m-d'),
        'check_out_date' => now()->addDays(7)->format('Y-m-d'),
        'status' => 'confirmed',
    ]);

    $newCheckIn = now()->addDays(6)->format('Y-m-d');
    $newCheckOut = now()->addDays(9)->format('Y-m-d');

    $this->actingAs($receptionist, 'sanctum')
        ->patchJson("/api/reception/reservations/{$reservation->id}", [
            'check_in_date' => $newCheckIn,
            'check_out_date' => $newCheckOut,
        ])
        ->assertOk()
        ->assertJsonPath('data.check_in_date', $newCheckIn)
        ->assertJsonPath('data.total_price', 120000);

    Mail::assertSent(App\Mail\ReservationModified::class);
});

it('rejects a modification that would overlap another reservation on the same room', function () {
    $receptionist = User::factory()->create(['role' => 'receptionist']);
    $room = Room::factory()->create();

    Reservation::factory()->create([
        'room_id' => $room->id,
        'check_in_date' => now()->addDays(20)->format('Y-m-d'),
        'check_out_date' => now()->addDays(22)->format('Y-m-d'),
        'status' => 'confirmed',
    ]);

    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'check_in_date' => now()->addDays(5)->format('Y-m-d'),
        'check_out_date' => now()->addDays(7)->format('Y-m-d'),
        'status' => 'confirmed',
    ]);

    $this->actingAs($receptionist, 'sanctum')
        ->patchJson("/api/reception/reservations/{$reservation->id}", [
            'check_in_date' => now()->addDays(21)->format('Y-m-d'),
            'check_out_date' => now()->addDays(23)->format('Y-m-d'),
        ])
        ->assertStatus(409);
});

<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('finds available rooms matching the search criteria', function () {
    $client = User::factory()->create();
    $roomType = RoomType::factory()->create();
    Room::factory()->create(['room_type_id' => $roomType->id, 'max_occupancy' => 2, 'status' => 'available']);

    $response = $this->actingAs($client, 'sanctum')->getJson(
        '/api/booking/search?check_in='.now()->addDays(5)->format('Y-m-d').'&check_out='.now()->addDays(7)->format('Y-m-d').'&adults=2'
    );

    $response->assertOk()
        ->assertJsonCount(1, 'rooms')
        ->assertJsonPath('meta.total', 1);
});

it('creates a reservation and sends a confirmation email', function () {
    $client = User::factory()->create();
    $room = Room::factory()->create(['status' => 'available', 'price_per_night' => 50000]);

    $response = $this->actingAs($client, 'sanctum')->postJson('/api/bookings', [
        'room_id' => $room->id,
        'check_in' => now()->addDays(5)->format('Y-m-d'),
        'check_out' => now()->addDays(7)->format('Y-m-d'),
        'adults' => 2,
    ]);

    $response->assertCreated()->assertJsonPath('data.total_price', 100000);
    expect(Room::find($room->id)->status)->toBe('occupied');
    Mail::assertSent(App\Mail\ReservationConfirmation::class);
});

it('rejects a reservation that overlaps an existing confirmed one on the same room', function () {
    $room = Room::factory()->create(['status' => 'occupied']);
    $existingGuest = User::factory()->create();
    Reservation::factory()->create([
        'room_id' => $room->id,
        'user_id' => $existingGuest->id,
        'check_in_date' => now()->addDays(5)->format('Y-m-d'),
        'check_out_date' => now()->addDays(8)->format('Y-m-d'),
        'status' => 'confirmed',
    ]);

    $client = User::factory()->create();

    $response = $this->actingAs($client, 'sanctum')->postJson('/api/bookings', [
        'room_id' => $room->id,
        'check_in' => now()->addDays(6)->format('Y-m-d'),
        'check_out' => now()->addDays(9)->format('Y-m-d'),
        'adults' => 1,
    ]);

    $response->assertStatus(409);
});

it('lets a client cancel a reservation more than 24h before check-in', function () {
    $client = User::factory()->create();
    $room = Room::factory()->create(['status' => 'occupied']);
    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'user_id' => $client->id,
        'check_in_date' => now()->addDays(3)->format('Y-m-d'),
        'status' => 'confirmed',
    ]);

    $this->actingAs($client, 'sanctum')
        ->postJson("/api/bookings/{$reservation->id}/cancel")
        ->assertOk()
        ->assertJsonPath('data.status', 'cancelled');

    expect(Room::find($room->id)->status)->toBe('available');
    Mail::assertSent(App\Mail\ReservationCancelled::class);
});

it('blocks cancellation less than 24h before check-in', function () {
    $client = User::factory()->create();
    $room = Room::factory()->create();
    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'user_id' => $client->id,
        'check_in_date' => now()->addHours(10)->format('Y-m-d'),
        'status' => 'confirmed',
    ]);

    $this->actingAs($client, 'sanctum')
        ->postJson("/api/bookings/{$reservation->id}/cancel")
        ->assertUnprocessable();
});

it("does not let a client see another client's reservation", function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder, 'sanctum')
        ->getJson("/api/bookings/{$reservation->id}")
        ->assertNotFound();
});

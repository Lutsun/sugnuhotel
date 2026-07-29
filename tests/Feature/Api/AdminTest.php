<?php

use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\User;

it('blocks non-admin users from the admin dashboard', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client, 'sanctum')
        ->getJson('/api/admin/dashboard')
        ->assertForbidden();
});

it('lets an admin create, update and delete a room type', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $create = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/room-types', [
        'name' => 'Suite Test',
        'base_price' => 120000,
        'max_occupancy' => 4,
    ]);
    $create->assertCreated();
    $roomTypeId = $create->json('data.id');

    $this->actingAs($admin, 'sanctum')
        ->putJson("/api/admin/room-types/{$roomTypeId}", [
            'name' => 'Suite Test Renommée',
            'base_price' => 130000,
            'max_occupancy' => 4,
        ])->assertOk()->assertJsonPath('data.name', 'Suite Test Renommée');

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/admin/room-types/{$roomTypeId}")
        ->assertOk();

    $this->assertDatabaseMissing('room_types', ['id' => $roomTypeId]);
});

it('prevents deleting a room type that still has rooms', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $roomType = RoomType::factory()->create();
    \App\Models\Room::factory()->create(['room_type_id' => $roomType->id]);

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/admin/room-types/{$roomType->id}")
        ->assertStatus(422);
});

it('toggles a service active status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $service = Service::factory()->create(['is_active' => true]);

    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/admin/services/{$service->id}/toggle-status")
        ->assertOk()
        ->assertJsonPath('data.is_active', false);
});

it('does not crash when deleting a user without reservations', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'client']);

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/admin/users/{$target->id}")
        ->assertOk();

    $this->assertDatabaseMissing('users', ['id' => $target->id]);
});

it('blocks deleting a user who has reservations', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'client']);
    Reservation::factory()->create(['user_id' => $target->id]);

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/admin/users/{$target->id}")
        ->assertStatus(422);

    $this->assertDatabaseHas('users', ['id' => $target->id]);
});

it('blocks an admin from deleting their own account', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/admin/users/{$admin->id}")
        ->assertStatus(422);
});

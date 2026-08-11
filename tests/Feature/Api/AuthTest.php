<?php

use App\Models\User;

it('registers a new client and returns a token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Nouveau Client',
        'email' => 'nouveau@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('user.role', 'client')
        ->assertJsonStructure(['user', 'token']);

    $this->assertDatabaseHas('users', ['email' => 'nouveau@example.com', 'role' => 'client']);
});

it('logs in with valid credentials and returns a token', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonStructure(['user', 'token']);
});

it('rejects login with invalid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnprocessable();
});

it('returns the authenticated user on /me', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

it('rejects unauthenticated access with json 401', function () {
    $this->getJson('/api/me')->assertUnauthorized();
});

<?php

use Laravel\Sanctum\Sanctum;

test('authenticated user can logout', function () {
    $user = $this->createUserWithRole('admin');
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertOk()
        ->assertJson(['message' => 'Logout realizado com sucesso.']);
});

test('token is deleted on logout', function () {
    $user = $this->createUserWithRole('admin');
    $token = $user->createToken('auth-token')->plainTextToken;

    $this->assertDatabaseCount('personal_access_tokens', 1);

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/logout')
        ->assertOk();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('unauthenticated user cannot logout', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});

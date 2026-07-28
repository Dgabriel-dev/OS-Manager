<?php

use Laravel\Sanctum\Sanctum;

test('user can view their profile', function () {
    $user = $this->createUserWithRole('admin');
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/me');

    $response->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.name', $user->name)
        ->assertJsonPath('user.email', $user->email);
});

test('user can update their profile', function () {
    $user = $this->createUserWithRole('admin');
    Sanctum::actingAs($user);

    $response = $this->putJson('/api/profile', [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $response->assertOk()
        ->assertJsonPath('user.name', 'Updated Name')
        ->assertJsonPath('user.email', 'updated@example.com');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('user can change their password', function () {
    $user = $this->createUserWithRole('admin');
    Sanctum::actingAs($user);

    $response = $this->putJson('/api/password', [
        'current_password' => 'password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertOk()
        ->assertJson(['message' => 'Senha alterada com sucesso.']);

    $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
});

test('user cannot change password with wrong current password', function () {
    $user = $this->createUserWithRole('admin');
    Sanctum::actingAs($user);

    $response = $this->putJson('/api/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['current_password']);
});

test('unauthenticated user cannot view profile', function () {
    $response = $this->getJson('/api/me');

    $response->assertStatus(401);
});

test('profile update validates email uniqueness', function () {
    $user = $this->createUserWithRole('admin');
    $other = $this->createUserWithRole('attendant', ['email' => 'taken@example.com']);
    Sanctum::actingAs($user);

    $response = $this->putJson('/api/profile', [
        'email' => 'taken@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

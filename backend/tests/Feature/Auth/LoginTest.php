<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can login with valid credentials', function () {
    $user = $this->createUserWithRole('admin');

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
});

test('user cannot login with invalid credentials', function () {
    $user = $this->createUserWithRole('admin');

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Credenciais inválidas.']);
});

test('user receives a token on login', function () {
    $user = $this->createUserWithRole('admin');

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk();
    $this->assertNotEmpty($response->json('token'));
});

test('inactive user cannot login', function () {
    $user = $this->createUserWithRole('admin', ['is_active' => false]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(403)
        ->assertJson(['message' => 'Sua conta está desativada. Entre em contato com o administrador.']);
});

test('login requires email and password', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

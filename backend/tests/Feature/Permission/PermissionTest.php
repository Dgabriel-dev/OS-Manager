<?php

use App\Models\Client;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('admin has all permissions', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->getJson('/api/clients');
    $response->assertOk();

    $response = $this->getJson('/api/service-orders');
    $response->assertOk();

    $response = $this->getJson('/api/stock/items');
    $response->assertOk();

    $response = $this->getJson('/api/financial/transactions');
    $response->assertOk();
});

test('attendant can manage clients', function () {
    $attendant = $this->actingAsRole('attendant');

    $response = $this->postJson('/api/clients', [
        'name' => 'Attendant Created',
        'cpf_cnpj' => '11111111111',
        'phone' => '11999990000',
    ]);
    $response->assertCreated();

    $client = Client::where('cpf_cnpj', '11111111111')->first();
    $response = $this->putJson("/api/clients/{$client->id}", ['name' => 'Updated by Attendant']);
    $response->assertOk();

    $response = $this->deleteJson("/api/clients/{$client->id}");
    $response->assertOk();
});

test('technician cannot manage clients', function () {
    $technician = $this->actingAsRole('technician');

    $response = $this->postJson('/api/clients', [
        'name' => 'Tech Attempt',
        'cpf_cnpj' => '22222222222',
        'phone' => '11999990001',
    ]);
    $response->assertStatus(403);
});

test('financial role can view transactions', function () {
    $financial = $this->actingAsRole('financial');

    $response = $this->getJson('/api/financial/transactions');
    $response->assertOk()
        ->assertJsonStructure([
            'data',
            'meta',
        ]);
});

test('financial role can create transactions', function () {
    $financial = $this->actingAsRole('financial');

    $response = $this->postJson('/api/financial/transactions', [
        'type' => 'income',
        'description' => 'Service payment',
        'amount' => 150.00,
        'payment_method' => 'pix',
        'payment_date' => now()->toDateString(),
        'status' => 'pending',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.type', 'income');
});

test('technician cannot create transactions', function () {
    $technician = $this->actingAsRole('technician');

    $response = $this->postJson('/api/financial/transactions', [
        'type' => 'income',
        'description' => 'Unauthorized',
        'amount' => 100.00,
        'payment_method' => 'cash',
        'payment_date' => now()->toDateString(),
        'status' => 'pending',
    ]);

    $response->assertStatus(403);
});

test('unauthenticated user gets 401', function () {
    $endpoints = [
        ['GET', '/api/clients'],
        ['GET', '/api/service-orders'],
        ['GET', '/api/stock/items'],
        ['GET', '/api/financial/transactions'],
        ['GET', '/api/dashboard'],
    ];

    foreach ($endpoints as [$method, $url]) {
        $response = $this->json($method, $url);
        $response->assertStatus(401, "Expected 401 for {$method} {$url}");
    }
});

test('attendant can create service orders', function () {
    $attendant = $this->actingAsRole('attendant');
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->postJson('/api/service-orders', [
        'client_id' => $client->id,
        'equipment_id' => $equipment->id,
        'priority' => 'low',
        'entry_date' => now()->toDateString(),
    ]);

    $response->assertCreated();
});

test('technician cannot create service orders', function () {
    $technician = $this->actingAsRole('technician');
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->postJson('/api/service-orders', [
        'client_id' => $client->id,
        'equipment_id' => $equipment->id,
        'priority' => 'low',
        'entry_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});

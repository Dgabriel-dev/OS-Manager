<?php

use App\Models\Client;
use Laravel\Sanctum\Sanctum;

test('admin can list clients', function () {
    $admin = $this->actingAsAdmin();
    $this->createClient(['name' => 'Client One']);
    $this->createClient(['name' => 'Client Two']);

    $response = $this->getJson('/api/clients');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('meta.total', 2);
});

test('admin can create a client', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->postJson('/api/clients', [
        'name' => 'New Client',
        'cpf_cnpj' => '12345678901',
        'phone' => '11999998888',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'New Client')
        ->assertJsonPath('data.cpf_cnpj', '12345678901');

    $this->assertDatabaseHas('clients', ['cpf_cnpj' => '12345678901']);
});

test('admin can view a client', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();

    $response = $this->getJson("/api/clients/{$client->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $client->id)
        ->assertJsonPath('data.name', $client->name);
});

test('admin can update a client', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();

    $response = $this->putJson("/api/clients/{$client->id}", [
        'name' => 'Updated Client',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Client');

    $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Updated Client']);
});

test('admin can delete a client', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();

    $response = $this->deleteJson("/api/clients/{$client->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Cliente removido com sucesso.']);

    $this->assertSoftDeleted('clients', ['id' => $client->id]);
});

test('client requires name and cpf_cnpj', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->postJson('/api/clients', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'cpf_cnpj', 'phone']);
});

test('cpf_cnpj must be unique', function () {
    $admin = $this->actingAsAdmin();
    $this->createClient(['cpf_cnpj' => '12345678901']);

    $response = $this->postJson('/api/clients', [
        'name' => 'Duplicate Client',
        'cpf_cnpj' => '12345678901',
        'phone' => '11999998888',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['cpf_cnpj']);
});

test('attendant can create clients', function () {
    $this->actingAsRole('attendant');

    $response = $this->postJson('/api/clients', [
        'name' => 'Attendant Client',
        'cpf_cnpj' => '98765432100',
        'phone' => '11888887777',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Attendant Client');
});

test('technician cannot create clients', function () {
    $this->actingAsRole('technician');

    $response = $this->postJson('/api/clients', [
        'name' => 'Tech Client',
        'cpf_cnpj' => '11122233344',
        'phone' => '11777776666',
    ]);

    $response->assertStatus(403);
});

test('unauthenticated user cannot access clients', function () {
    $response = $this->getJson('/api/clients');

    $response->assertStatus(401);
});

test('admin can restore a soft-deleted client', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();

    $this->deleteJson("/api/clients/{$client->id}");
    $this->assertSoftDeleted('clients', ['id' => $client->id]);

    $response = $this->postJson("/api/clients/{$client->id}/restore");

    $response->assertOk()
        ->assertJson(['message' => 'Cliente restaurado com sucesso.']);

    $this->assertDatabaseHas('clients', ['id' => $client->id, 'deleted_at' => null]);
});

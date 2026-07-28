<?php

use App\Models\Equipment;
use Laravel\Sanctum\Sanctum;

test('admin can list equipment', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $this->createEquipment($client);
    $this->createEquipment($client, ['category' => 'Monitor']);

    $response = $this->getJson('/api/equipment');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'category']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('meta.total', 2);
});

test('admin can create equipment', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();

    $response = $this->postJson('/api/equipment', [
        'client_id' => $client->id,
        'category' => 'Notebook',
        'brand' => 'HP',
        'model' => 'Pavilion',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.category', 'Notebook')
        ->assertJsonPath('data.brand', 'HP');

    $this->assertDatabaseHas('equipments', ['category' => 'Notebook']);
});

test('equipment requires client_id and category', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->postJson('/api/equipment', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['client_id', 'category']);
});

test('equipment belongs to a client', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient(['name' => 'Equipment Owner']);

    $response = $this->postJson('/api/equipment', [
        'client_id' => $client->id,
        'category' => 'Desktop',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.client.id', $client->id);
});

test('admin can view equipment', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->getJson("/api/equipment/{$equipment->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $equipment->id)
        ->assertJsonPath('data.category', $equipment->category);
});

test('admin can update equipment', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->putJson("/api/equipment/{$equipment->id}", [
        'category' => 'Updated Category',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.category', 'Updated Category');

    $this->assertDatabaseHas('equipments', ['id' => $equipment->id, 'category' => 'Updated Category']);
});

test('admin can delete equipment', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->deleteJson("/api/equipment/{$equipment->id}");

    $response->assertOk();

    $this->assertSoftDeleted('equipments', ['id' => $equipment->id]);
});

test('equipment listing is paginated', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();

    for ($i = 0; $i < 20; $i++) {
        $this->createEquipment($client, ['category' => "Category {$i}"]);
    }

    $response = $this->getJson('/api/equipment?per_page=5');

    $response->assertOk()
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonPath('meta.total', 20)
        ->assertJsonCount(5, 'data');
});

test('unauthenticated user cannot access equipment', function () {
    $response = $this->getJson('/api/equipment');

    $response->assertStatus(401);
});

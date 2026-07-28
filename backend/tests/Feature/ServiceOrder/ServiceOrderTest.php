<?php

use App\Models\OrderHistory;
use App\Models\ServiceOrder;
use Laravel\Sanctum\Sanctum;

test('admin can list service orders', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $this->createServiceOrder($client, $equipment);
    $this->createServiceOrder($client, $equipment, ['status' => 'in_progress']);

    $response = $this->getJson('/api/service-orders');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'order_number', 'status']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('meta.total', 2);
});

test('admin can create a service order', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->postJson('/api/service-orders', [
        'client_id' => $client->id,
        'equipment_id' => $equipment->id,
        'priority' => 'high',
        'entry_date' => now()->toDateString(),
        'notes' => 'Customer reported boot issue',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.client.id', $client->id)
        ->assertJsonPath('data.equipment.id', $equipment->id);

    $this->assertDatabaseHas('service_orders', [
        'client_id' => $client->id,
        'equipment_id' => $equipment->id,
    ]);
});

test('service order auto-generates order number', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->postJson('/api/service-orders', [
        'client_id' => $client->id,
        'equipment_id' => $equipment->id,
        'priority' => 'medium',
        'entry_date' => now()->toDateString(),
    ]);

    $response->assertCreated();

    $orderNumber = $response->json('data.order_number');
    $this->assertMatchesRegularExpression('/^OS-\d{4}-\d{6}$/', $orderNumber);
});

test('admin can update service order status', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment, ['status' => 'pending']);

    $response = $this->putJson("/api/service-orders/{$order->id}/status", [
        'status' => 'in_progress',
        'notes' => 'Starting diagnosis',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'in_progress');

    $this->assertDatabaseHas('service_orders', [
        'id' => $order->id,
        'status' => 'in_progress',
    ]);
});

test('status change is recorded in history', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment, ['status' => 'pending']);

    $this->putJson("/api/service-orders/{$order->id}/status", [
        'status' => 'in_progress',
    ]);

    $this->assertDatabaseHas('order_histories', [
        'service_order_id' => $order->id,
        'action' => 'status_changed',
    ]);
});

test('technician can view assigned orders', function () {
    $technician = $this->actingAsRole('technician');
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment, [
        'technician_id' => $technician->id,
    ]);

    $response = $this->getJson('/api/service-orders');

    $response->assertOk();
    $orderIds = collect($response->json('data'))->pluck('id')->toArray();
    $this->assertContains($order->id, $orderIds);
});

test('unauthenticated user cannot access orders', function () {
    $response = $this->getJson('/api/service-orders');

    $response->assertStatus(401);
});

test('service order requires client_id and equipment_id', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->postJson('/api/service-orders', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['client_id', 'equipment_id']);
});

test('service order validates priority', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);

    $response = $this->postJson('/api/service-orders', [
        'client_id' => $client->id,
        'equipment_id' => $equipment->id,
        'priority' => 'invalid_priority',
        'entry_date' => now()->toDateString(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['priority']);
});

test('admin can view a service order', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment);

    $response = $this->getJson("/api/service-orders/{$order->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $order->id)
        ->assertJsonPath('data.order_number', $order->order_number);
});

test('admin can update a service order', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment);

    $response = $this->putJson("/api/service-orders/{$order->id}", [
        'notes' => 'Updated notes',
        'estimated_value' => 250.00,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.notes', 'Updated notes');
});

test('admin can delete a service order', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment);

    $response = $this->deleteJson("/api/service-orders/{$order->id}");

    $response->assertOk();

    $this->assertSoftDeleted('service_orders', ['id' => $order->id]);
});

test('service order validates status values', function () {
    $admin = $this->actingAsAdmin();
    $client = $this->createClient();
    $equipment = $this->createEquipment($client);
    $order = $this->createServiceOrder($client, $equipment);

    $response = $this->putJson("/api/service-orders/{$order->id}/status", [
        'status' => 'invalid_status',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

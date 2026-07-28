<?php

use App\Models\StockItem;
use App\Models\StockMovement;
use Laravel\Sanctum\Sanctum;

test('admin can list stock items', function () {
    $admin = $this->actingAsAdmin();
    $this->createStockItem(['name' => 'Mouse']);
    $this->createStockItem(['name' => 'Keyboard']);

    $response = $this->getJson('/api/stock/items');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('meta.total', 2);
});

test('admin can create stock item', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->postJson('/api/stock/items', [
        'name' => 'New Component',
        'quantity' => 50,
        'purchase_price' => 25.00,
        'sale_price' => 45.00,
        'minimum_quantity' => 10,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'New Component')
        ->assertJsonPath('data.quantity', 50);

    $this->assertDatabaseHas('stock_items', ['name' => 'New Component']);
});

test('stock item requires name and quantity', function () {
    $admin = $this->actingAsAdmin();

    $response = $this->postJson('/api/stock/items', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'quantity']);
});

test('admin can adjust stock', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem(['quantity' => 10]);

    $response = $this->putJson("/api/stock/items/{$item->id}/adjust", [
        'quantity' => 5,
        'type' => 'entry',
        'notes' => 'Restocking',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.quantity', 15);

    $this->assertDatabaseHas('stock_items', ['id' => $item->id, 'quantity' => 15]);
});

test('stock adjustment creates movement record', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem(['quantity' => 10]);

    $this->putJson("/api/stock/items/{$item->id}/adjust", [
        'quantity' => 5,
        'type' => 'entry',
        'notes' => 'Test adjustment',
    ]);

    $this->assertDatabaseHas('stock_movements', [
        'stock_item_id' => $item->id,
        'type' => 'entry',
        'quantity' => 5,
        'previous_quantity' => 10,
        'new_quantity' => 15,
    ]);
});

test('low stock items are flagged', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem([
        'quantity' => 2,
        'minimum_quantity' => 10,
    ]);

    $response = $this->getJson('/api/stock/items');

    $response->assertOk();

    $itemData = collect($response->json('data'))->firstWhere('id', $item->id);
    $this->assertTrue($itemData['is_low_stock']);
});

test('admin can view stock item', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem(['name' => 'Test Item']);

    $response = $this->getJson("/api/stock/items/{$item->id}");

    $response->assertOk()
        ->assertJsonPath('data.name', 'Test Item');
});

test('admin can update stock item', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem();

    $response = $this->putJson("/api/stock/items/{$item->id}", [
        'name' => 'Updated Item',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Item');
});

test('admin can delete stock item', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem();

    $response = $this->deleteJson("/api/stock/items/{$item->id}");

    $response->assertOk();

    $this->assertSoftDeleted('stock_items', ['id' => $item->id]);
});

test('stock adjustment requires quantity and type', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem();

    $response = $this->putJson("/api/stock/items/{$item->id}/adjust", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['quantity', 'type']);
});

test('stock adjustment validates type values', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem();

    $response = $this->putJson("/api/stock/items/{$item->id}/adjust", [
        'quantity' => 5,
        'type' => 'invalid_type',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

test('unauthenticated user cannot access stock', function () {
    $response = $this->getJson('/api/stock/items');

    $response->assertStatus(401);
});

test('stock movements are listed', function () {
    $admin = $this->actingAsAdmin();
    $item = $this->createStockItem(['quantity' => 0]);

    $this->putJson("/api/stock/items/{$item->id}/adjust", [
        'quantity' => 10,
        'type' => 'entry',
    ]);

    $response = $this->getJson('/api/stock/movements');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'type', 'quantity']],
            'meta',
        ]);
});

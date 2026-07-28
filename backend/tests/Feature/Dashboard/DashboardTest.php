<?php

use Laravel\Sanctum\Sanctum;

test('authenticated user can access dashboard', function () {
    $user = $this->actingAsAdmin();

    $response = $this->getJson('/api/dashboard');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'stats',
                'monthly_orders',
                'revenue_chart',
                'top_equipments',
                'recent_orders',
            ],
        ]);
});

test('dashboard returns correct stat keys', function () {
    $user = $this->actingAsAdmin();

    $response = $this->getJson('/api/dashboard');

    $response->assertOk();

    $stats = $response->json('data.stats');
    $this->assertArrayHasKey('total_clients', $stats);
    $this->assertArrayHasKey('total_equipment', $stats);
    $this->assertArrayHasKey('orders_by_status', $stats);
    $this->assertArrayHasKey('monthly_revenue', $stats);
    $this->assertArrayHasKey('low_stock_count', $stats);
});

test('unauthenticated user cannot access dashboard', function () {
    $response = $this->getJson('/api/dashboard');

    $response->assertStatus(401);
});

test('dashboard returns zero counts for empty database', function () {
    $user = $this->actingAsAdmin();

    $response = $this->getJson('/api/dashboard');

    $response->assertOk();

    $stats = $response->json('data.stats');
    $this->assertEquals(0, $stats['total_clients']);
    $this->assertEquals(0, $stats['total_equipment']);
    $this->assertEquals(0, $stats['low_stock_count']);
});

test('dashboard returns revenue chart as array', function () {
    $user = $this->actingAsAdmin();

    $response = $this->getJson('/api/dashboard');

    $response->assertOk();

    $revenueChart = $response->json('data.revenue_chart');
    $this->assertIsArray($revenueChart);
    $this->assertNotEmpty($revenueChart);

    $firstMonth = $revenueChart[0];
    $this->assertArrayHasKey('month', $firstMonth);
    $this->assertArrayHasKey('income', $firstMonth);
    $this->assertArrayHasKey('expense', $firstMonth);
});

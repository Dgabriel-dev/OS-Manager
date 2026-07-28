<?php

namespace Tests;

use App\Models\Client;
use App\Models\Equipment;
use App\Models\Role;
use App\Models\ServiceOrder;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(
            ['name' => $roleName],
            ['description' => ucfirst($roleName) . ' role']
        );

        return User::factory()->create(array_merge([
            'role_id' => $role->id,
            'is_active' => true,
        ], $attributes));
    }

    protected function createClient(array $attributes = []): Client
    {
        return Client::create(array_merge([
            'name' => fake()->name(),
            'cpf_cnpj' => fake()->unique()->numerify('##############'),
            'phone' => fake()->phoneNumber(),
        ], $attributes));
    }

    protected function createEquipment(Client $client, array $attributes = []): Equipment
    {
        return Equipment::create(array_merge([
            'client_id' => $client->id,
            'category' => 'Computador',
            'brand' => 'Dell',
            'model' => 'Inspiron',
        ], $attributes));
    }

    protected function createServiceOrder(Client $client, Equipment $equipment, array $attributes = []): ServiceOrder
    {
        return ServiceOrder::create(array_merge([
            'order_number' => 'OS-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'client_id' => $client->id,
            'equipment_id' => $equipment->id,
            'priority' => 'medium',
            'status' => 'pending',
            'entry_date' => now()->toDateString(),
            'warranty_days' => 30,
        ], $attributes));
    }

    protected function createStockItem(array $attributes = []): StockItem
    {
        return StockItem::create(array_merge([
            'name' => fake()->words(2, true),
            'purchase_price' => fake()->randomFloat(2, 5, 100),
            'sale_price' => fake()->randomFloat(2, 10, 200),
            'quantity' => 10,
            'minimum_quantity' => 5,
        ], $attributes));
    }

    protected function actingAsAdmin(): User
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);
        return $admin;
    }

    protected function actingAsRole(string $roleName): User
    {
        $user = $this->createUserWithRole($roleName);
        Sanctum::actingAs($user);
        return $user;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Clientes
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            // Equipamentos
            'equipments.view',
            'equipments.create',
            'equipments.update',
            'equipments.delete',
            // Ordens de Serviço
            'service_orders.view',
            'service_orders.create',
            'service_orders.update',
            'service_orders.delete',
            'service_orders.update_status',
            // Estoque
            'stock.view',
            'stock.create',
            'stock.update',
            'stock.delete',
            'stock.adjust',
            // Financeiro
            'financial.view',
            'financial.create',
            'financial.update',
            'financial.delete',
            // Usuários
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            // Relatórios
            'reports.view',
            // Configurações
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'description' => str_replace('.', ' ', ucfirst(str_replace('.', ': ', $permission))),
            ]);
        }
    }
}

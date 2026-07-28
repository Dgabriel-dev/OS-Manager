<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'admin')->first();
        $attendant = Role::where('name', 'attendant')->first();
        $technician = Role::where('name', 'technician')->first();
        $financial = Role::where('name', 'financial')->first();

        // Admin: todas as permissões
        $admin->permissions()->attach(Permission::all());

        // Atendente: clientes, equipamentos, OS (visualizar/criar/atualizar), relatórios
        $attendantPermissions = Permission::whereIn('name', [
            'clients.view', 'clients.create', 'clients.update', 'clients.delete',
            'equipments.view', 'equipments.create', 'equipments.update', 'equipments.delete',
            'service_orders.view', 'service_orders.create', 'service_orders.update',
            'reports.view',
        ])->get();
        $attendant->permissions()->attach($attendantPermissions);

        // Técnico: visualizar OS, alterar status, visualizar equipamentos
        $technicianPermissions = Permission::whereIn('name', [
            'service_orders.view',
            'service_orders.update_status',
            'equipments.view',
        ])->get();
        $technician->permissions()->attach($technicianPermissions);

        // Financeiro: financeiro, relatórios, visualizar clientes e OS
        $financialPermissions = Permission::whereIn('name', [
            'financial.view', 'financial.create', 'financial.update', 'financial.delete',
            'reports.view',
            'clients.view',
            'service_orders.view',
        ])->get();
        $financial->permissions()->attach($financialPermissions);
    }
}

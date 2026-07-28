<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrador do sistema - acesso total'],
            ['name' => 'attendant', 'description' => 'Atendente - gerencia clientes, equipamentos e OS'],
            ['name' => 'technician', 'description' => 'Técnico - visualiza OS, altera status, insere laudos e peças'],
            ['name' => 'financial', 'description' => 'Financeiro - pagamentos, relatórios e caixa'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

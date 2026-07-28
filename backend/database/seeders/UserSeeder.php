<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $attendantRole = Role::where('name', 'attendant')->first();
        $technicianRole = Role::where('name', 'technician')->first();
        $financialRole = Role::where('name', 'financial')->first();

        // Administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@osassist.com.br',
            'password' => Hash::make('password'),
            'phone' => '(11) 99999-9999',
            'role_id' => $adminRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Atendente
        User::create([
            'name' => 'Maria Atendente',
            'email' => 'atendente@osassist.com.br',
            'password' => Hash::make('password'),
            'phone' => '(11) 98888-8888',
            'role_id' => $attendantRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Técnico
        User::create([
            'name' => 'João Técnico',
            'email' => 'tecnico@osassist.com.br',
            'password' => Hash::make('password'),
            'phone' => '(11) 97777-7777',
            'role_id' => $technicianRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Financeiro
        User::create([
            'name' => 'Carlos Financeiro',
            'email' => 'financeiro@osassist.com.br',
            'password' => Hash::make('password'),
            'phone' => '(11) 96666-6666',
            'role_id' => $financialRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}

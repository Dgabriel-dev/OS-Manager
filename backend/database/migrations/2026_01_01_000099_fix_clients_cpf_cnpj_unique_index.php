<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE clients DROP CONSTRAINT IF EXISTS clients_cpf_cnpj_unique');
        DB::statement('CREATE UNIQUE INDEX clients_cpf_cnpj_unique ON clients (cpf_cnpj) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS clients_cpf_cnpj_unique');
        DB::statement('ALTER TABLE clients ADD CONSTRAINT clients_cpf_cnpj_unique UNIQUE (cpf_cnpj)');
    }
};

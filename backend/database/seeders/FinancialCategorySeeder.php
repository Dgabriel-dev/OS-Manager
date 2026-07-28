<?php

namespace Database\Seeders;

use App\Models\FinancialCategory;
use Illuminate\Database\Seeder;

class FinancialCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorias de receita
        $incomeCategories = [
            ['name' => 'Serviço de reparo', 'type' => 'income'],
            ['name' => 'Venda de peças', 'type' => 'income'],
            ['name' => 'Consulta técnica', 'type' => 'income'],
            ['name' => 'Outras receitas', 'type' => 'income'],
        ];

        foreach ($incomeCategories as $category) {
            FinancialCategory::create($category);
        }

        // Categorias de despesa
        $expenseCategories = [
            ['name' => 'Compra de peças', 'type' => 'expense'],
            ['name' => 'Aluguel', 'type' => 'expense'],
            ['name' => 'Energia elétrica', 'type' => 'expense'],
            ['name' => 'Internet e telefone', 'type' => 'expense'],
            ['name' => 'Salários', 'type' => 'expense'],
            ['name' => 'Impostos', 'type' => 'expense'],
            ['name' => 'Material de escritório', 'type' => 'expense'],
            ['name' => 'Outras despesas', 'type' => 'expense'],
        ];

        foreach ($expenseCategories as $category) {
            FinancialCategory::create($category);
        }
    }
}

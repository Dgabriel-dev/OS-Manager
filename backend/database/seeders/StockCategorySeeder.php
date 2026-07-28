<?php

namespace Database\Seeders;

use App\Models\StockCategory;
use Illuminate\Database\Seeder;

class StockCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Peças', 'description' => 'Peças de reposição para reparo'],
            ['name' => 'Acessórios', 'description' => 'Acessórios e periféricos'],
            ['name' => 'Ferramentas', 'description' => 'Ferramentas para manutenção'],
            ['name' => 'Consumíveis', 'description' => 'Materiais de consumo (tintas, solventes, etc)'],
            ['name' => 'Outros', 'description' => 'Outros itens de estoque'],
        ];

        foreach ($categories as $category) {
            StockCategory::create($category);
        }
    }
}

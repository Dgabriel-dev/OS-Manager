<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'stock_category_id' => ['nullable', 'exists:stock_categories,id'],
            'internal_code' => ['nullable', 'string', 'max:50', 'unique:stock_items,internal_code'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'minimum_quantity' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'unit_of_measure' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'stock_category_id.exists' => 'A categoria selecionada não existe.',
            'internal_code.unique' => 'Este código interno já está em uso.',
            'quantity.required' => 'O campo quantidade é obrigatório.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade não pode ser negativa.',
            'purchase_price.numeric' => 'O preço de compra deve ser um número.',
            'sale_price.numeric' => 'O preço de venda deve ser um número.',
        ];
    }
}

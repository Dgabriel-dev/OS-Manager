<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('item');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'stock_category_id' => ['nullable', 'exists:stock_categories,id'],
            'internal_code' => ['nullable', 'string', 'max:50', Rule::unique('stock_items', 'internal_code')->ignore($itemId)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'minimum_quantity' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'unit_of_measure' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'internal_code.unique' => 'Este código interno já está em uso por outro item.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade não pode ser negativa.',
            'purchase_price.numeric' => 'O preço de compra deve ser um número.',
            'sale_price.numeric' => 'O preço de venda deve ser um número.',
        ];
    }
}

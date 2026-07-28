<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'nullable', 'exists:clients,id'],
            'payment_method' => ['sometimes', 'nullable', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'in:pending,paid,cancelled'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.sale_category_id' => ['nullable', 'exists:sale_categories,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.cost_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.min' => 'A venda deve ter pelo menos um item.',
            'items.*.name.required_with' => 'O nome do item é obrigatório.',
            'items.*.quantity.required_with' => 'A quantidade do item é obrigatória.',
            'items.*.unit_price.required_with' => 'O preço unitário do item é obrigatório.',
        ];
    }
}

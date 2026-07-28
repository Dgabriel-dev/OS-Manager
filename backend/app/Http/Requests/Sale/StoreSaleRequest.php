<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['nullable', 'in:pending,paid,cancelled'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.sale_category_id' => ['nullable', 'exists:sale_categories,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.cost_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'A venda deve ter pelo menos um item.',
            'items.min' => 'A venda deve ter pelo menos um item.',
            'items.*.name.required' => 'O nome do item é obrigatório.',
            'items.*.quantity.required' => 'A quantidade do item é obrigatória.',
            'items.*.unit_price.required' => 'O preço unitário do item é obrigatório.',
        ];
    }
}

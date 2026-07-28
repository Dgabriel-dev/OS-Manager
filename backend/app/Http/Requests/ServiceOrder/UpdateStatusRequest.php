<?php

namespace App\Http\Requests\ServiceOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:open,in_progress,waiting_parts,completed,delivered,cancelled'],
            'notes' => ['nullable', 'string'],
            'used_parts' => ['nullable', 'array'],
            'used_parts.*.stock_item_id' => ['required_with:used_parts', 'exists:stock_items,id'],
            'used_parts.*.quantity' => ['required_with:used_parts', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O status deve ser um dos valores válidos: pendente, em andamento, aguardando peças, concluído, entregue, cancelado.',
            'used_parts.*.stock_item_id.required_with' => 'O ID do item de estoque é obrigatório quando há peças utilizadas.',
            'used_parts.*.stock_item_id.exists' => 'O item de estoque selecionado não existe.',
            'used_parts.*.quantity.required_with' => 'A quantidade é obrigatória quando há peças utilizadas.',
            'used_parts.*.quantity.min' => 'A quantidade deve ser pelo menos 1.',
        ];
    }
}

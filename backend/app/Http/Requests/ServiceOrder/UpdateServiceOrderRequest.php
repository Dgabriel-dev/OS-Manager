<?php

namespace App\Http\Requests\ServiceOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'exists:clients,id'],
            'equipment_id' => ['sometimes', 'exists:equipments,id'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'final_value' => ['nullable', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
            'entry_date' => ['nullable', 'date'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'O cliente selecionado não existe.',
            'equipment_id.exists' => 'O equipamento selecionado não existe.',
            'technician_id.exists' => 'O técnico selecionado não existe.',
            'priority.in' => 'A prioridade deve ser: baixa, média, alta ou urgente.',
        ];
    }
}

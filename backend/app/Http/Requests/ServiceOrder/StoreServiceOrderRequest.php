<?php

namespace App\Http\Requests\ServiceOrder;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'equipment_id' => ['required', 'exists:equipments,id'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
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
            'client_id.required' => 'O campo cliente é obrigatório.',
            'client_id.exists' => 'O cliente selecionado não existe.',
            'equipment_id.required' => 'O campo equipamento é obrigatório.',
            'equipment_id.exists' => 'O equipamento selecionado não existe.',
            'technician_id.exists' => 'O técnico selecionado não existe.',
            'priority.in' => 'A prioridade deve ser: baixa, média, alta ou urgente.',
            'estimated_value.numeric' => 'O valor estimado deve ser um número.',
        ];
    }
}

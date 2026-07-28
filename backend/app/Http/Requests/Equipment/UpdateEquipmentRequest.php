<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'exists:clients,id'],
            'category' => ['sometimes', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'accessories_delivered' => ['nullable', 'string'],
            'physical_state' => ['nullable', 'string', 'max:50'],
            'reported_defect' => ['nullable', 'string'],
            'technical_diagnosis' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'O cliente selecionado não existe.',
        ];
    }
}

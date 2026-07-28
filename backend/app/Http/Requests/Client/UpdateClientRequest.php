<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('client');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'cpf_cnpj' => ['sometimes', 'string', 'max:18', Rule::unique('clients', 'cpf_cnpj')->ignore($clientId)->withoutTrashed()],
            'phone' => ['sometimes', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'cep' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado para outro cliente.',
            'email.email' => 'O campo e-mail deve ser um endereço válido.',
        ];
    }
}

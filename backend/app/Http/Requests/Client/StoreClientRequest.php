<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cpf_cnpj' => ['required', 'string', 'max:18', Rule::unique('clients', 'cpf_cnpj')->withoutTrashed()],
            'phone' => ['required', 'string', 'max:20'],
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
            'name.required' => 'O campo nome é obrigatório.',
            'cpf_cnpj.required' => 'O campo CPF/CNPJ é obrigatório.',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'phone.required' => 'O campo telefone é obrigatório.',
            'email.email' => 'O campo e-mail deve ser um endereço válido.',
        ];
    }
}

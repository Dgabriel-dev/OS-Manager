<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:income,expense'],
            'financial_category_id' => ['nullable', 'exists:financial_categories,id'],
            'service_order_id' => ['nullable', 'exists:service_orders,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:pending,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'O campo tipo é obrigatório.',
            'type.in' => 'O tipo deve ser: receita ou despesa.',
            'financial_category_id.exists' => 'A categoria financeira selecionada não existe.',
            'service_order_id.exists' => 'A ordem de serviço selecionada não existe.',
            'description.required' => 'O campo descrição é obrigatório.',
            'amount.required' => 'O campo valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'status.in' => 'O status deve ser: pendente, concluído ou cancelado.',
        ];
    }
}

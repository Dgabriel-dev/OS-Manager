<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpf_cnpj' => $this->cpf_cnpj,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'cep' => $this->cep,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'observations' => $this->observations,
            'orders_count' => $this->whenCounted('serviceOrders'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

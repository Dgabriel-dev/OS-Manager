<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'category' => new FinancialCategoryResource($this->whenLoaded('category')),
            'service_order' => new ServiceOrderResource($this->whenLoaded('serviceOrder')),
            'description' => $this->description,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_date' => $this->payment_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

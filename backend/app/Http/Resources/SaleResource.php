<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalCost = 0;
        foreach ($this->whenLoaded('items') as $item) {
            $totalCost += ($item->cost_price ?? 0) * $item->quantity;
        }

        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'user' => new UserResource($this->whenLoaded('user')),
            'total_amount' => $this->total_amount,
            'total_cost' => $totalCost,
            'profit' => $this->total_amount - $totalCost,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

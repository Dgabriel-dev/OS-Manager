<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_item' => new StockItemResource($this->whenLoaded('stockItem')),
            'type' => $this->type,
            'quantity' => $this->quantity,
            'previous_quantity' => $this->previous_quantity,
            'new_quantity' => $this->new_quantity,
            'user' => new UserResource($this->whenLoaded('user')),
            'service_order_id' => $this->service_order_id,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

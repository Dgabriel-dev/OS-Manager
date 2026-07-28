<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'client' => new ClientResource($this->whenLoaded('client')),
            'equipment' => new EquipmentResource($this->whenLoaded('equipment')),
            'technician' => new UserResource($this->whenLoaded('technician')),
            'priority' => $this->priority,
            'status' => $this->status,
            'estimated_value' => $this->estimated_value,
            'final_value' => $this->final_value,
            'warranty_days' => $this->warranty_days,
            'entry_date' => $this->entry_date?->toDateString(),
            'estimated_delivery_date' => $this->estimated_delivery_date?->toDateString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'notes' => $this->notes,
            'items' => ServiceOrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

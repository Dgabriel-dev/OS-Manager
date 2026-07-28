<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'category' => $this->category,
            'brand' => $this->brand,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'color' => $this->color,
            'accessories_delivered' => $this->accessories_delivered,
            'physical_state' => $this->physical_state,
            'reported_defect' => $this->reported_defect,
            'technical_diagnosis' => $this->technical_diagnosis,
            'files' => $this->files,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

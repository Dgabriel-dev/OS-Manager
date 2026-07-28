<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'internal_code' => $this->internal_code,
            'barcode' => $this->barcode,
            'category' => new StockCategoryResource($this->whenLoaded('category')),
            'supplier' => $this->supplier,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'quantity' => $this->quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'location' => $this->location,
            'is_low_stock' => $this->is_low_stock,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

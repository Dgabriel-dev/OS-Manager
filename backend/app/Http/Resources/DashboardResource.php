<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_clients' => $this->resource['total_clients'] ?? 0,
            'total_equipment' => $this->resource['total_equipment'] ?? 0,
            'orders_by_status' => $this->resource['orders_by_status'] ?? [],
            'monthly_revenue' => $this->resource['monthly_revenue'] ?? 0,
            'low_stock_count' => $this->resource['low_stock_count'] ?? 0,
            'low_stock_items' => StockItemResource::collection($this->resource['low_stock_items'] ?? collect()),
        ];
    }
}

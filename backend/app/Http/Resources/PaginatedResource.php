<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->resource['data'] ?? [],
            'meta' => [
                'current_page' => $this->resource['current_page'] ?? 1,
                'last_page' => $this->resource['last_page'] ?? 1,
                'per_page' => $this->resource['per_page'] ?? 15,
                'total' => $this->resource['total'] ?? 0,
            ],
        ];
    }
}

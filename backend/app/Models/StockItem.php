<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'stock_category_id',
        'internal_code',
        'barcode',
        'supplier',
        'purchase_price',
        'sale_price',
        'quantity',
        'minimum_quantity',
        'location',
        'unit_of_measure',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'quantity' => 'integer',
        'minimum_quantity' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(StockCategory::class, 'stock_category_id');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class, 'stock_item_id');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->minimum_quantity;
    }
}

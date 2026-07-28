<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'user_id',
        'service_order_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_quantity' => 'integer',
        'new_quantity' => 'integer',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}

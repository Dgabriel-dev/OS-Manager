<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'client_id',
        'equipment_id',
        'technician_id',
        'priority',
        'status',
        'estimated_value',
        'final_value',
        'warranty_days',
        'entry_date',
        'estimated_delivery_date',
        'delivered_at',
        'notes',
        'internal_notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'final_value' => 'decimal:2',
        'entry_date' => 'date',
        'estimated_delivery_date' => 'date',
        'delivered_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'service_order_id');
    }

    public function items()
    {
        return $this->hasMany(ServiceOrderItem::class, 'service_order_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'service_order_id');
    }
}

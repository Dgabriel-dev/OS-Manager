<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'financial_category_id',
        'service_order_id',
        'description',
        'amount',
        'payment_method',
        'payment_date',
        'due_date',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(FinancialCategory::class, 'financial_category_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

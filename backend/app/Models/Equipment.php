<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipments';

    protected $fillable = [
        'client_id',
        'category',
        'brand',
        'model',
        'serial_number',
        'color',
        'accessories_delivered',
        'physical_state',
        'reported_defect',
        'technical_diagnosis',
        'files',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'financial_category_id');
    }
}

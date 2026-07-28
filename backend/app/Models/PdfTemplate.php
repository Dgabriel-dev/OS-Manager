<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}

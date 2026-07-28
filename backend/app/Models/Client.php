<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cpf_cnpj',
        'phone',
        'whatsapp',
        'email',
        'cep',
        'address',
        'city',
        'state',
        'observations',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }
}

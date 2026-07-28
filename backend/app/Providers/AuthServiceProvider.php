<?php

namespace App\Providers;

use App\Policies\ClientPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\ServiceOrderPolicy;
use App\Policies\StockItemPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\ServiceOrder;
use App\Models\StockItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Equipment::class => EquipmentPolicy::class,
        ServiceOrder::class => ServiceOrderPolicy::class,
        StockItem::class => StockItemPolicy::class,
        Transaction::class => TransactionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

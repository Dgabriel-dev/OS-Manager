<?php

namespace App\Policies;

use App\Models\StockItem;
use App\Models\User;

class StockItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StockItem $item): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'attendant']);
    }

    public function update(User $user, StockItem $item): bool
    {
        return in_array($user->role?->name, ['admin', 'attendant']);
    }

    public function delete(User $user, StockItem $item): bool
    {
        return $user->isAdmin();
    }

    public function adjustStock(User $user, StockItem $item): bool
    {
        return in_array($user->role?->name, ['admin', 'attendant']);
    }
}

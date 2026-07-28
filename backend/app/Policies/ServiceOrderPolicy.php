<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;

class ServiceOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ServiceOrder $order): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'attendant']);
    }

    public function update(User $user, ServiceOrder $order): bool
    {
        return in_array($user->role?->name, ['admin', 'attendant']);
    }

    public function updateStatus(User $user, ServiceOrder $order): bool
    {
        if ($user->isAdmin() || $user->role?->name === 'attendant') {
            return true;
        }

        if ($user->role?->name === 'technician' && $order->technician_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, ServiceOrder $order): bool
    {
        return $user->isAdmin();
    }
}

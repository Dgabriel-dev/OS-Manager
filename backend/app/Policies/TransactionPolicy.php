<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'financial']);
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return in_array($user->role?->name, ['admin', 'financial']);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin();
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BankAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BankAccount');
    }

    public function view(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('View:BankAccount');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BankAccount');
    }

    public function update(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('Update:BankAccount');
    }

    public function delete(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('Delete:BankAccount');
    }
}

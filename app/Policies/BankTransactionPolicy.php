<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BankTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankTransactionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BankTransaction');
    }

    public function view(AuthUser $authUser, BankTransaction $bankTransaction): bool
    {
        return $authUser->can('View:BankTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BankTransaction');
    }

    public function update(AuthUser $authUser, BankTransaction $bankTransaction): bool
    {
        return $authUser->can('Update:BankTransaction');
    }

    public function delete(AuthUser $authUser, BankTransaction $bankTransaction): bool
    {
        return $authUser->can('Delete:BankTransaction');
    }

    public function restore(AuthUser $authUser, BankTransaction $bankTransaction): bool
    {
        return $authUser->can('Restore:BankTransaction');
    }

    public function forceDelete(AuthUser $authUser, BankTransaction $bankTransaction): bool
    {
        return $authUser->can('ForceDelete:BankTransaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BankTransaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BankTransaction');
    }

    public function replicate(AuthUser $authUser, BankTransaction $bankTransaction): bool
    {
        return $authUser->can('Replicate:BankTransaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BankTransaction');
    }

}
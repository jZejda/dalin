<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserCredit;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserCreditPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserCredit');
    }

    public function view(AuthUser $authUser, UserCredit $userCredit): bool
    {
        return $authUser->can('View:UserCredit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserCredit');
    }

    public function update(AuthUser $authUser, UserCredit $userCredit): bool
    {
        return $authUser->can('Update:UserCredit');
    }

    public function delete(AuthUser $authUser, UserCredit $userCredit): bool
    {
        return $authUser->can('Delete:UserCredit');
    }

    public function restore(AuthUser $authUser, UserCredit $userCredit): bool
    {
        return $authUser->can('Restore:UserCredit');
    }

    public function forceDelete(AuthUser $authUser, UserCredit $userCredit): bool
    {
        return $authUser->can('ForceDelete:UserCredit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserCredit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserCredit');
    }

    public function replicate(AuthUser $authUser, UserCredit $userCredit): bool
    {
        return $authUser->can('Replicate:UserCredit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserCredit');
    }

}

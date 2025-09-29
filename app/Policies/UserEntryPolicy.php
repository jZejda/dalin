<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserEntry');
    }

    public function view(AuthUser $authUser, UserEntry $userEntry): bool
    {
        return $authUser->can('View:UserEntry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserEntry');
    }

    public function update(AuthUser $authUser, UserEntry $userEntry): bool
    {
        return $authUser->can('Update:UserEntry');
    }

    public function delete(AuthUser $authUser, UserEntry $userEntry): bool
    {
        return $authUser->can('Delete:UserEntry');
    }

    public function restore(AuthUser $authUser, UserEntry $userEntry): bool
    {
        return $authUser->can('Restore:UserEntry');
    }

    public function forceDelete(AuthUser $authUser, UserEntry $userEntry): bool
    {
        return $authUser->can('ForceDelete:UserEntry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserEntry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserEntry');
    }

    public function replicate(AuthUser $authUser, UserEntry $userEntry): bool
    {
        return $authUser->can('Replicate:UserEntry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserEntry');
    }

}

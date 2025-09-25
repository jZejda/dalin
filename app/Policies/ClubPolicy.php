<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Club;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClubPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Club');
    }

    public function view(AuthUser $authUser, Club $club): bool
    {
        return $authUser->can('View:Club');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Club');
    }

    public function update(AuthUser $authUser, Club $club): bool
    {
        return $authUser->can('Update:Club');
    }

    public function delete(AuthUser $authUser, Club $club): bool
    {
        return $authUser->can('Delete:Club');
    }

    public function restore(AuthUser $authUser, Club $club): bool
    {
        return $authUser->can('Restore:Club');
    }

    public function forceDelete(AuthUser $authUser, Club $club): bool
    {
        return $authUser->can('ForceDelete:Club');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Club');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Club');
    }

    public function replicate(AuthUser $authUser, Club $club): bool
    {
        return $authUser->can('Replicate:Club');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Club');
    }

}
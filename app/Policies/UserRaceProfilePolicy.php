<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserRaceProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRaceProfilePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserRaceProfile');
    }

    public function view(AuthUser $authUser, UserRaceProfile $userRaceProfile): bool
    {
        return $authUser->can('View:UserRaceProfile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserRaceProfile');
    }

    public function update(AuthUser $authUser, UserRaceProfile $userRaceProfile): bool
    {
        return $authUser->can('Update:UserRaceProfile');
    }

    public function delete(AuthUser $authUser, UserRaceProfile $userRaceProfile): bool
    {
        return $authUser->can('Delete:UserRaceProfile');
    }

    public function restore(AuthUser $authUser, UserRaceProfile $userRaceProfile): bool
    {
        return $authUser->can('Restore:UserRaceProfile');
    }

    public function forceDelete(AuthUser $authUser, UserRaceProfile $userRaceProfile): bool
    {
        return $authUser->can('ForceDelete:UserRaceProfile');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserRaceProfile');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserRaceProfile');
    }

    public function replicate(AuthUser $authUser, UserRaceProfile $userRaceProfile): bool
    {
        return $authUser->can('Replicate:UserRaceProfile');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserRaceProfile');
    }

}

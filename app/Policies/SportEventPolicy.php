<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SportEvent;
use Illuminate\Auth\Access\HandlesAuthorization;

class SportEventPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SportEvent');
    }

    public function view(AuthUser $authUser, SportEvent $sportEvent): bool
    {
        return $authUser->can('View:SportEvent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SportEvent');
    }

    public function update(AuthUser $authUser, SportEvent $sportEvent): bool
    {
        return $authUser->can('Update:SportEvent');
    }

    public function delete(AuthUser $authUser, SportEvent $sportEvent): bool
    {
        return $authUser->can('Delete:SportEvent');
    }

    public function restore(AuthUser $authUser, SportEvent $sportEvent): bool
    {
        return $authUser->can('Restore:SportEvent');
    }

    public function forceDelete(AuthUser $authUser, SportEvent $sportEvent): bool
    {
        return $authUser->can('ForceDelete:SportEvent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SportEvent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SportEvent');
    }

    public function replicate(AuthUser $authUser, SportEvent $sportEvent): bool
    {
        return $authUser->can('Replicate:SportEvent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SportEvent');
    }

}

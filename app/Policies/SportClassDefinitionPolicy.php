<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SportClassDefinition;
use Illuminate\Auth\Access\HandlesAuthorization;

class SportClassDefinitionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SportClassDefinition');
    }

    public function view(AuthUser $authUser, SportClassDefinition $sportClassDefinition): bool
    {
        return $authUser->can('View:SportClassDefinition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SportClassDefinition');
    }

    public function update(AuthUser $authUser, SportClassDefinition $sportClassDefinition): bool
    {
        return $authUser->can('Update:SportClassDefinition');
    }

    public function delete(AuthUser $authUser, SportClassDefinition $sportClassDefinition): bool
    {
        return $authUser->can('Delete:SportClassDefinition');
    }

    public function restore(AuthUser $authUser, SportClassDefinition $sportClassDefinition): bool
    {
        return $authUser->can('Restore:SportClassDefinition');
    }

    public function forceDelete(AuthUser $authUser, SportClassDefinition $sportClassDefinition): bool
    {
        return $authUser->can('ForceDelete:SportClassDefinition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SportClassDefinition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SportClassDefinition');
    }

    public function replicate(AuthUser $authUser, SportClassDefinition $sportClassDefinition): bool
    {
        return $authUser->can('Replicate:SportClassDefinition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SportClassDefinition');
    }

}

<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SportEventExport;
use Illuminate\Auth\Access\HandlesAuthorization;

class SportEventExportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SportEventExport');
    }

    public function view(AuthUser $authUser, SportEventExport $sportEventExport): bool
    {
        return $authUser->can('View:SportEventExport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SportEventExport');
    }

    public function update(AuthUser $authUser, SportEventExport $sportEventExport): bool
    {
        return $authUser->can('Update:SportEventExport');
    }

    public function delete(AuthUser $authUser, SportEventExport $sportEventExport): bool
    {
        return $authUser->can('Delete:SportEventExport');
    }

    public function restore(AuthUser $authUser, SportEventExport $sportEventExport): bool
    {
        return $authUser->can('Restore:SportEventExport');
    }

    public function forceDelete(AuthUser $authUser, SportEventExport $sportEventExport): bool
    {
        return $authUser->can('ForceDelete:SportEventExport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SportEventExport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SportEventExport');
    }

    public function replicate(AuthUser $authUser, SportEventExport $sportEventExport): bool
    {
        return $authUser->can('Replicate:SportEventExport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SportEventExport');
    }

}
<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ContentCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContentCategory');
    }

    public function view(AuthUser $authUser, ContentCategory $contentCategory): bool
    {
        return $authUser->can('View:ContentCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContentCategory');
    }

    public function update(AuthUser $authUser, ContentCategory $contentCategory): bool
    {
        return $authUser->can('Update:ContentCategory');
    }

    public function delete(AuthUser $authUser, ContentCategory $contentCategory): bool
    {
        return $authUser->can('Delete:ContentCategory');
    }

    public function restore(AuthUser $authUser, ContentCategory $contentCategory): bool
    {
        return $authUser->can('Restore:ContentCategory');
    }

    public function forceDelete(AuthUser $authUser, ContentCategory $contentCategory): bool
    {
        return $authUser->can('ForceDelete:ContentCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContentCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContentCategory');
    }

    public function replicate(AuthUser $authUser, ContentCategory $contentCategory): bool
    {
        return $authUser->can('Replicate:ContentCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContentCategory');
    }

}
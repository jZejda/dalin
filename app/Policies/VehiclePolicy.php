<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VehiclePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Vehicle');
    }

    public function view(AuthUser $authUser, Vehicle $vehicle): bool
    {
        return $authUser->can('View:Vehicle') || $this->owns($authUser, $vehicle);
    }

    public function create(AuthUser $authUser): bool
    {
        // Každý přihlášený člen může vytvářet vlastní vozidla,
        // klubová vozidla hlídá VehicleResource::canCreate().
        return true;
    }

    public function update(AuthUser $authUser, Vehicle $vehicle): bool
    {
        return $authUser->can('Update:Vehicle') || $this->owns($authUser, $vehicle);
    }

    public function delete(AuthUser $authUser, Vehicle $vehicle): bool
    {
        return $authUser->can('Delete:Vehicle') || $this->owns($authUser, $vehicle);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Vehicle');
    }

    public function restore(AuthUser $authUser, Vehicle $vehicle): bool
    {
        return $authUser->can('Restore:Vehicle');
    }

    public function forceDelete(AuthUser $authUser, Vehicle $vehicle): bool
    {
        return $authUser->can('ForceDelete:Vehicle');
    }

    private function owns(AuthUser $authUser, Vehicle $vehicle): bool
    {
        return $vehicle->user_id !== null && $vehicle->user_id === $authUser->getAuthIdentifier();
    }
}

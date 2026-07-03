<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TransportOffer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TransportOfferPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, TransportOffer $transportOffer): bool
    {
        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, TransportOffer $transportOffer): bool
    {
        return $authUser->can('Update:TransportOffer') || $this->owns($authUser, $transportOffer);
    }

    public function delete(AuthUser $authUser, TransportOffer $transportOffer): bool
    {
        return $authUser->can('Delete:TransportOffer') || $this->owns($authUser, $transportOffer);
    }

    private function owns(AuthUser $authUser, TransportOffer $transportOffer): bool
    {
        return $transportOffer->user_id === $authUser->getAuthIdentifier();
    }
}

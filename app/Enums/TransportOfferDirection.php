<?php

declare(strict_types=1);

namespace App\Enums;

enum TransportOfferDirection: string
{
    case OnlyThere = 'onlyThere';
    case OnlyBack = 'onlyBack';
    case BothDirection = 'bothDirection';

}

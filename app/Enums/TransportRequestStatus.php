<?php

declare(strict_types=1);

namespace App\Enums;

enum TransportRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::Pending->value => __('transport.request_status_enum.'.self::Pending->value),
            self::Approved->value => __('transport.request_status_enum.'.self::Approved->value),
            self::Rejected->value => __('transport.request_status_enum.'.self::Rejected->value),
            self::Cancelled->value => __('transport.request_status_enum.'.self::Cancelled->value),
        ];
    }

    public function label(): string
    {
        return __('transport.request_status_enum.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Cancelled => 'heroicon-o-minus-circle',
        };
    }
}

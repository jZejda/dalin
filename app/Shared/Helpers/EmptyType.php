<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

final class EmptyType
{
    public static function stringNotEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_string($value) || strlen($value) === 0) {
                return false;
            }
        }

        return true;
    }

    public static function stringEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_string($value) || strlen($value) !== 0) {
                return false;
            }
        }

        return true;
    }

    public static function intNotEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_int($value) || $value === 0) {
                return false;
            }
        }

        return true;
    }

    public static function intEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_int($value) || $value !== 0) {
                return false;
            }
        }

        return true;
    }

    public static function arrayNotEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_array($value) || count($value) === 0) {
                return false;
            }
        }

        return true;
    }

    public static function arrayEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_array($value) || count($value) !== 0) {
                return false;
            }
        }

        return true;
    }

    public static function floatNotEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_float($value) || $value === 0.0) {
                return false;
            }
        }

        return true;
    }

    public static function floatEmpty(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!is_float($value) || $value !== 0.0) {
                return false;
            }
        }

        return true;
    }
}

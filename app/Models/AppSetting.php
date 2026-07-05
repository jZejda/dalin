<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\AppSetting
 *
 * @property int $id
 * @property string $key
 * @property mixed $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AppSetting extends Model
{
    use HasFactory;

    public const string TRANSPORT_MODULE_ENABLED = 'transport.enabled';

    public const string EVENT_PAYMENTS_MODULE_ENABLED = 'event_payments.enabled';

    public const string SERVICE_ORDERS_MODULE_ENABLED = 'service_orders.enabled';

    public const string MARKETPLACE_MODULE_ENABLED = 'marketplace.enabled';

    public const string CLUB_FULL_NAME = 'club.full_name';

    public const string CLUB_PRIMARY_BANK_ACCOUNT_NUMBER = 'club.primary_bank_account_number';

    public const string CLUB_PRIMARY_BANK_ACCOUNT_NAME = 'club.primary_bank_account_name';

    public const string CLUB_IBAN = 'club.iban';

    public const string CLUB_USER_CREDIT_LIMIT = 'club.user_credit_limit';

    public const string CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX = 'club.regular_membership_fees_prefix';

    public const string CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX = 'club.extra_membership_fees_prefix';

    public const string CLUB_TECHNICAL_EMAIL = 'club.technical_email';

    /**
     * Club settings editable in the admin panel, mapped to the config keys
     * they override. The `abbr` is intentionally missing — it drives the ORIS
     * integration and logo asset path, so it stays file-only.
     *
     * @var array<string, string>
     */
    public const array CLUB_CONFIG_MAP = [
        self::CLUB_FULL_NAME => 'site-config.club.full_name',
        self::CLUB_PRIMARY_BANK_ACCOUNT_NUMBER => 'site-config.club.primary_bank_account_number',
        self::CLUB_PRIMARY_BANK_ACCOUNT_NAME => 'site-config.club.primary_bank_account_name',
        self::CLUB_IBAN => 'site-config.club.iban',
        self::CLUB_USER_CREDIT_LIMIT => 'site-config.club.user_credit_limit',
        self::CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX => 'site-config.club.regular_membership_fees_prefix',
        self::CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX => 'site-config.club.extra_membership_fees_prefix',
        self::CLUB_TECHNICAL_EMAIL => 'site-config.club.technical_email',
    ];

    /** @var list<string> */
    protected $fillable = [
        'key',
        'value',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'json',
    ];

    protected static function booted(): void
    {
        self::saved(static function (AppSetting $setting): void {
            Cache::forget(self::cacheKey($setting->getOriginal('key') ?? $setting->key));
            Cache::forget(self::cacheKey($setting->key));
        });

        self::deleted(static function (AppSetting $setting): void {
            Cache::forget(self::cacheKey($setting->key));
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever(
            self::cacheKey($key),
            static fn (): mixed => self::query()->firstWhere('key', $key)?->value,
        );

        return $value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function boolean(string $key, bool $default = false): bool
    {
        return (bool) self::get($key, $default);
    }

    public static function isTransportModuleEnabled(): bool
    {
        return self::boolean(self::TRANSPORT_MODULE_ENABLED);
    }

    public static function isEventPaymentsModuleEnabled(): bool
    {
        return self::boolean(self::EVENT_PAYMENTS_MODULE_ENABLED);
    }

    public static function isServiceOrdersModuleEnabled(): bool
    {
        return self::boolean(self::SERVICE_ORDERS_MODULE_ENABLED);
    }

    public static function isMarketplaceModuleEnabled(): bool
    {
        return self::boolean(self::MARKETPLACE_MODULE_ENABLED);
    }

    /**
     * Overrides file/env club config with values saved in the admin panel.
     * A null (never saved or cleared) value keeps the config file default.
     */
    public static function applyClubConfigOverrides(): void
    {
        foreach (self::CLUB_CONFIG_MAP as $settingKey => $configKey) {
            $value = self::get($settingKey);

            if ($value !== null) {
                config()->set($configKey, $value);
            }
        }
    }

    private static function cacheKey(string $key): string
    {
        return 'app_setting.'.$key;
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Override;

/**
 * App\Models\AppSetting
 *
 * @property int $id
 * @property string $key
 * @property mixed $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'key',
    'value',
])]
class AppSetting extends Model
{
    use HasFactory;

    public const string TRANSPORT_MODULE_ENABLED = 'transport.enabled';

    public const string EVENT_PAYMENTS_MODULE_ENABLED = 'event_payments.enabled';

    public const string SERVICE_ORDERS_MODULE_ENABLED = 'service_orders.enabled';

    public const string MARKETPLACE_MODULE_ENABLED = 'marketplace.enabled';

    public const string BANK_MODULE_ENABLED = 'bank.enabled';

    public const string MAPY_MODULE_ENABLED = 'mapy.enabled';

    public const string MAPY_API_KEY = 'mapy.api_key';

    public const string CLUB_FULL_NAME = 'club.full_name';

    public const string CLUB_PRIMARY_BANK_ACCOUNT_NUMBER = 'club.primary_bank_account_number';

    public const string CLUB_PRIMARY_BANK_ACCOUNT_NAME = 'club.primary_bank_account_name';

    public const string CLUB_IBAN = 'club.iban';

    public const string CLUB_USER_CREDIT_LIMIT = 'club.user_credit_limit';

    public const string CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX = 'club.regular_membership_fees_prefix';

    public const string CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX = 'club.extra_membership_fees_prefix';

    public const string CLUB_TECHNICAL_EMAIL = 'club.technical_email';

    public const string SEO_DESCRIPTION = 'seo.description';

    public const string SEO_IMAGE = 'seo.image';

    public const string SEO_LOGO = 'seo.logo';

    public const string SEO_SAME_AS = 'seo.same_as';

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

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    #[Override]
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

    public static function isBankModuleEnabled(): bool
    {
        return self::boolean(self::BANK_MODULE_ENABLED);
    }

    public static function isMapyModuleEnabled(): bool
    {
        return self::boolean(self::MAPY_MODULE_ENABLED);
    }

    /**
     * Whether the Mapy.cz tile layer should be offered on the leaflet maps:
     * the module must be turned on AND an API key must actually be saved.
     */
    public static function isMapyLayerActive(): bool
    {
        return self::isMapyModuleEnabled() && self::getMapyApiKey() !== null;
    }

    /**
     * Decrypts the stored key. Treated as "not saved" (rather than a fatal error) if it can't
     * be decrypted with the current APP_KEY — e.g. after a key rotation or a restored backup.
     */
    public static function getMapyApiKey(): ?string
    {
        $encrypted = self::get(self::MAPY_API_KEY);

        if ($encrypted === null) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $exception) {
            Log::warning('Stored Mapy.cz API key could not be decrypted, treating it as unset.', [
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public static function setMapyApiKey(?string $apiKey): void
    {
        self::set(self::MAPY_API_KEY, $apiKey !== null ? Crypt::encryptString($apiKey) : null);
    }

    public static function getSeoDescription(): ?string
    {
        return self::nonEmptyString(self::get(self::SEO_DESCRIPTION));
    }

    /**
     * Path of the default social-sharing image on the `public` disk.
     */
    public static function getSeoImagePath(): ?string
    {
        return self::nonEmptyString(self::get(self::SEO_IMAGE));
    }

    /**
     * Path of the club logo (raster, for schema.org `logo`) on the `public` disk.
     */
    public static function getSeoLogoPath(): ?string
    {
        return self::nonEmptyString(self::get(self::SEO_LOGO));
    }

    /**
     * Club profiles on social networks, rendered as schema.org `sameAs`.
     *
     * @return list<string>
     */
    public static function getSeoSameAs(): array
    {
        $value = self::get(self::SEO_SAME_AS);

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $url): bool => is_string($url) && $url !== ''));
    }

    private static function nonEmptyString(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
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

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

    private static function cacheKey(string $key): string
    {
        return 'app_setting.'.$key;
    }
}

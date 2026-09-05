<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Maps an OpenWeatherMap "weather condition id" (see
 * https://openweathermap.org/weather-conditions) to one of our SVG weather
 * icons, rendered by resources/views/components/weather-icon.blade.php.
 */
enum WeatherIconType: string
{
    case Sun = 'sun';
    case CloudSun = 'cloud-sun';
    case Clouds = 'clouds';
    case Cloudy = 'cloudy';
    case CloudDrizzle = 'cloud-drizzle';
    case CloudRain = 'cloud-rain';
    case CloudRainHeavy = 'cloud-rain-heavy';
    case CloudSleet = 'cloud-sleet';
    case CloudSnow = 'cloud-snow';
    case CloudLightning = 'cloud-lightning';
    case CloudLightningRain = 'cloud-lightning-rain';
    case CloudFog = 'cloud-fog';
    case CloudFog2 = 'cloud-fog2';
    case CloudHaze = 'cloud-haze';
    case CloudHaze2 = 'cloud-haze2';
    case Wind = 'wind';
    case Tornado = 'tornado';

    public static function fromOpenWeatherId(?int $id): self
    {
        return match (true) {
            $id === 800 => self::Sun,
            $id === 801 => self::CloudSun,
            $id === 802 => self::Clouds,
            in_array($id, [803, 804], true) => self::Cloudy,
            $id !== null && $id >= 300 && $id <= 321 => self::CloudDrizzle,
            in_array($id, [500, 501], true) => self::CloudRain,
            in_array($id, [502, 503, 504], true) => self::CloudRainHeavy,
            in_array($id, [511, 611, 612, 613, 615, 616], true) => self::CloudSleet,
            in_array($id, [520, 521, 522, 531], true) => self::CloudRainHeavy,
            in_array($id, [600, 601, 602, 620, 621, 622], true) => self::CloudSnow,
            in_array($id, [210, 211, 212, 221], true) => self::CloudLightning,
            in_array($id, [200, 201, 202, 230, 231, 232], true) => self::CloudLightningRain,
            $id === 701 => self::CloudFog,
            $id === 741 => self::CloudFog2,
            in_array($id, [711, 762], true) => self::CloudHaze2,
            $id === 721 => self::CloudHaze,
            in_array($id, [731, 751, 761, 771], true) => self::Wind,
            $id === 781 => self::Tornado,
            default => self::Cloudy,
        };
    }
}

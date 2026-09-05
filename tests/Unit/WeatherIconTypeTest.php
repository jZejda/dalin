<?php

declare(strict_types=1);

use App\Enums\WeatherIconType;

test('maps clear and cloud coverage ids', function (): void {
    expect(WeatherIconType::fromOpenWeatherId(800))->toBe(WeatherIconType::Sun)
        ->and(WeatherIconType::fromOpenWeatherId(801))->toBe(WeatherIconType::CloudSun)
        ->and(WeatherIconType::fromOpenWeatherId(802))->toBe(WeatherIconType::Clouds)
        ->and(WeatherIconType::fromOpenWeatherId(803))->toBe(WeatherIconType::Cloudy)
        ->and(WeatherIconType::fromOpenWeatherId(804))->toBe(WeatherIconType::Cloudy);
});

test('maps thunderstorm ids depending on whether rain is mentioned', function (): void {
    expect(WeatherIconType::fromOpenWeatherId(210))->toBe(WeatherIconType::CloudLightning)
        ->and(WeatherIconType::fromOpenWeatherId(211))->toBe(WeatherIconType::CloudLightning)
        ->and(WeatherIconType::fromOpenWeatherId(212))->toBe(WeatherIconType::CloudLightning)
        ->and(WeatherIconType::fromOpenWeatherId(221))->toBe(WeatherIconType::CloudLightning)
        ->and(WeatherIconType::fromOpenWeatherId(200))->toBe(WeatherIconType::CloudLightningRain)
        ->and(WeatherIconType::fromOpenWeatherId(201))->toBe(WeatherIconType::CloudLightningRain)
        ->and(WeatherIconType::fromOpenWeatherId(202))->toBe(WeatherIconType::CloudLightningRain)
        ->and(WeatherIconType::fromOpenWeatherId(230))->toBe(WeatherIconType::CloudLightningRain)
        ->and(WeatherIconType::fromOpenWeatherId(231))->toBe(WeatherIconType::CloudLightningRain)
        ->and(WeatherIconType::fromOpenWeatherId(232))->toBe(WeatherIconType::CloudLightningRain);
});

test('maps drizzle and rain ids by intensity', function (): void {
    expect(WeatherIconType::fromOpenWeatherId(300))->toBe(WeatherIconType::CloudDrizzle)
        ->and(WeatherIconType::fromOpenWeatherId(321))->toBe(WeatherIconType::CloudDrizzle)
        ->and(WeatherIconType::fromOpenWeatherId(500))->toBe(WeatherIconType::CloudRain)
        ->and(WeatherIconType::fromOpenWeatherId(501))->toBe(WeatherIconType::CloudRain)
        ->and(WeatherIconType::fromOpenWeatherId(502))->toBe(WeatherIconType::CloudRainHeavy)
        ->and(WeatherIconType::fromOpenWeatherId(504))->toBe(WeatherIconType::CloudRainHeavy)
        ->and(WeatherIconType::fromOpenWeatherId(511))->toBe(WeatherIconType::CloudSleet)
        ->and(WeatherIconType::fromOpenWeatherId(520))->toBe(WeatherIconType::CloudRainHeavy)
        ->and(WeatherIconType::fromOpenWeatherId(531))->toBe(WeatherIconType::CloudRainHeavy);
});

test('maps snow and sleet ids', function (): void {
    expect(WeatherIconType::fromOpenWeatherId(600))->toBe(WeatherIconType::CloudSnow)
        ->and(WeatherIconType::fromOpenWeatherId(602))->toBe(WeatherIconType::CloudSnow)
        ->and(WeatherIconType::fromOpenWeatherId(622))->toBe(WeatherIconType::CloudSnow)
        ->and(WeatherIconType::fromOpenWeatherId(611))->toBe(WeatherIconType::CloudSleet)
        ->and(WeatherIconType::fromOpenWeatherId(616))->toBe(WeatherIconType::CloudSleet);
});

test('maps atmosphere ids', function (): void {
    expect(WeatherIconType::fromOpenWeatherId(701))->toBe(WeatherIconType::CloudFog)
        ->and(WeatherIconType::fromOpenWeatherId(741))->toBe(WeatherIconType::CloudFog2)
        ->and(WeatherIconType::fromOpenWeatherId(711))->toBe(WeatherIconType::CloudHaze2)
        ->and(WeatherIconType::fromOpenWeatherId(762))->toBe(WeatherIconType::CloudHaze2)
        ->and(WeatherIconType::fromOpenWeatherId(721))->toBe(WeatherIconType::CloudHaze)
        ->and(WeatherIconType::fromOpenWeatherId(731))->toBe(WeatherIconType::Wind)
        ->and(WeatherIconType::fromOpenWeatherId(751))->toBe(WeatherIconType::Wind)
        ->and(WeatherIconType::fromOpenWeatherId(761))->toBe(WeatherIconType::Wind)
        ->and(WeatherIconType::fromOpenWeatherId(771))->toBe(WeatherIconType::Wind)
        ->and(WeatherIconType::fromOpenWeatherId(781))->toBe(WeatherIconType::Tornado);
});

test('falls back to cloudy for unknown or missing ids', function (): void {
    expect(WeatherIconType::fromOpenWeatherId(null))->toBe(WeatherIconType::Cloudy)
        ->and(WeatherIconType::fromOpenWeatherId(999))->toBe(WeatherIconType::Cloudy);
});

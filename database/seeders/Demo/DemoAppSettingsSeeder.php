<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

class DemoAppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Enable all optional modules so the demo shows everything the system offers
        AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
        AppSetting::set(AppSetting::EVENT_PAYMENTS_MODULE_ENABLED, true);
        AppSetting::set(AppSetting::SERVICE_ORDERS_MODULE_ENABLED, true);
        AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, true);

        // Club settings visible in the Config cluster settings page
        AppSetting::set(AppSetting::CLUB_FULL_NAME, 'SK Demo Orientace');
        AppSetting::set(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NUMBER, '123456789/0100');
        AppSetting::set(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NAME, 'SK Demo Orientace, z. s.');
        AppSetting::set(AppSetting::CLUB_IBAN, 'CZ6501000000000123456789');
        AppSetting::set(AppSetting::CLUB_USER_CREDIT_LIMIT, -2000);
        AppSetting::set(AppSetting::CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX, '111');
        AppSetting::set(AppSetting::CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX, '888');
        AppSetting::set(AppSetting::CLUB_TECHNICAL_EMAIL, 'technik@demo.cz');

        // Club accent colour used by the branded e-mail layout (Club settings → Club colours)
        AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#1f7a5c');

        // Public website SEO / social sharing (Club settings → Public website section)
        AppSetting::set(
            AppSetting::SEO_DESCRIPTION,
            'SK Demo Orientace je oddíl orientačního běhu pro děti i dospělé. Trénujeme v lesích kolem Brna, jezdíme na závody po celé republice a pořádáme vlastní akce.',
        );
        AppSetting::set(AppSetting::SEO_IMAGE, $this->createSharingImage());
        AppSetting::set(AppSetting::SEO_SAME_AS, [
            'https://www.facebook.com/skdemoorientace',
            'https://www.instagram.com/skdemoorientace',
        ]);
    }

    /**
     * Default 1200×630 social-sharing image, cropped from the frontend hero background.
     */
    private function createSharingImage(): string
    {
        $path = 'seo/demo-sharing.jpg';
        $disk = Storage::disk('public');
        $disk->makeDirectory('seo');

        Image::load(public_path('images/terrain-hero.webp'))
            ->fit(Fit::Crop, 1200, 630)
            ->save($disk->path($path));

        return $path;
    }
}

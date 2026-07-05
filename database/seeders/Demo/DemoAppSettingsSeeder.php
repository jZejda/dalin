<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

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
    }
}

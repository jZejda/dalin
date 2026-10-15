<?php

declare(strict_types=1);

namespace App\Filament\Tables\Columns;

use App\Enums\PaymentCategory;
use App\Models\UserCredit;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Override;

/**
 * "Payment type" column of a UserCredit table: category badge + label, with the additional service
 * name on a second line. Use it everywhere credits are listed so the payment type looks the same.
 */
class PaymentCategoryColumn extends TextColumn
{
    #[Override]
    public static function getDefaultName(): ?string
    {
        return 'payment_category';
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('user-credit.table.payment_category_title'))
            ->state(fn (UserCredit $record): PaymentCategory => PaymentCategory::fromCredit($record))
            // The badge component renders the icon itself; stop Filament adding the enum icon again.
            ->icon(false)
            ->html()
            ->formatStateUsing(fn (PaymentCategory $state, UserCredit $record): HtmlString => new HtmlString(
                (string) view('components.payment-category-identity', [
                    'category' => $state,
                    'description' => self::serviceDescription($record),
                    'size' => 'sm',
                ])
            ));
    }

    /**
     * Second line: the additional service name, marked when it is a reversal.
     */
    private static function serviceDescription(UserCredit $record): ?string
    {
        if ($record->sport_service_id === null) {
            return null;
        }

        $name = $record->sportService->service_name_cz ?? '#'.$record->sport_service_id;

        return $record->amount > 0
            ? $name.' · '.__('user-credit.table.payment_category_reversal')
            : $name;
    }
}

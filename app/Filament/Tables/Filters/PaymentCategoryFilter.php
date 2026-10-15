<?php

declare(strict_types=1);

namespace App\Filament\Tables\Filters;

use App\Enums\PaymentCategory;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Override;

/**
 * Multi-select "Payment type" filter for UserCredit tables; pairs with PaymentCategoryColumn.
 */
class PaymentCategoryFilter extends SelectFilter
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
            ->options(PaymentCategory::options())
            ->multiple()
            ->query(function (Builder $query, array $data): Builder {
                $categories = array_map(PaymentCategory::from(...), $data['values'] ?? []);

                if ($categories === []) {
                    return $query;
                }

                return $query->where(function (Builder $query) use ($categories): void {
                    foreach ($categories as $category) {
                        $query->orWhere(fn (Builder $q) => $category->constrain($q));
                    }
                });
            });
    }
}

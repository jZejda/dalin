<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Enums\SiChipSport;
use App\Enums\SiChipType;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

/**
 * SI chips of a race profile: the primary chip (`si` column) + up to 2 additional chips.
 * Shared by the member's own profile form and the admin relation manager.
 */
final class SiChipsSection
{
    public const int MAX_EXTRA_CHIPS = 2;

    /** Upper bound of the unsigned INT columns the chip numbers are stored in. */
    public const int MAX_SI = 4294967295;

    public static function make(): Section
    {
        return Section::make(__('user-race-profile.common.si'))
            ->schema([
                TextInput::make('si')
                    ->label(__('user-race-profile.form.si'))
                    ->helperText(__('user-race-profile.form.si_helper'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(self::MAX_SI)
                    ->columnSpan(1),
                Select::make('si_sport')
                    ->label(__('si-chip.form.sport'))
                    ->options(SiChipSport::class)
                    ->default(SiChipSport::All->value)
                    ->selectablePlaceholder(false),
                Select::make('si_type')
                    ->label(__('si-chip.form.type'))
                    ->options(SiChipType::class)
                    ->default(SiChipType::All->value)
                    ->selectablePlaceholder(false),
                Repeater::make('chips')
                    ->label(__('si-chip.form.extra_chips'))
                    ->helperText(__('si-chip.form.extra_chips_helper'))
                    ->relationship('chips')
                    ->orderColumn('sort_order')
                    ->maxItems(self::MAX_EXTRA_CHIPS)
                    ->addActionLabel(__('si-chip.form.add_chip'))
                    ->defaultItems(0)
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('si')
                            ->label(__('si-chip.form.si'))
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(self::MAX_SI)
                            ->distinct()
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                    $primary = $get('../../si');

                                    if (filled($primary) && (int) $value === (int) $primary) {
                                        $fail(__('si-chip.validation.same_as_primary'));
                                    }
                                },
                            ]),
                        Select::make('sport')
                            ->label(__('si-chip.form.sport'))
                            ->options(SiChipSport::class)
                            ->default(SiChipSport::All->value)
                            ->selectablePlaceholder(false),
                        Select::make('type')
                            ->label(__('si-chip.form.type'))
                            ->options(SiChipType::class)
                            ->default(SiChipType::All->value)
                            ->selectablePlaceholder(false),
                    ]),
            ])
            ->columns(3);
    }
}

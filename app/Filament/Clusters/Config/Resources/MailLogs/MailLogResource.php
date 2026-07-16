<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\MailLogs;

use App\Enums\MailSource;
use App\Filament\Clusters\Config\ConfigCluster;
use App\Filament\Clusters\Config\Resources\MailLogs\Pages\ListMailLogs;
use App\Models\MailLog;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MailLogResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = MailLog::class;

    protected static ?string $cluster = ConfigCluster::class;

    protected static ?int $navigationSort = 90;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    public static function getNavigationLabel(): string
    {
        return __('mail-log.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('mail-log.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('mail-log.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('mail-log.sent_at'))
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->icon('heroicon-o-calendar-days')
                    ->sortable(),
                TextColumn::make('recipient')
                    ->label(__('mail-log.recipient'))
                    ->wrap()
                    ->searchable()
                    ->copyable(),
                TextColumn::make('subject')
                    ->label(__('mail-log.subject'))
                    ->wrap()
                    ->searchable()
                    ->limit(60),
                TextColumn::make('mailable_short')
                    ->label(__('mail-log.mailable'))
                    ->badge()
                    ->color('gray')
                    ->placeholder(__('mail-log.mailable_placeholder')),
                TextColumn::make('source_type')
                    ->label(__('mail-log.source'))
                    ->badge()
                    ->formatStateUsing(fn (MailSource $state): string => $state->label())
                    ->color(fn (MailSource $state): string => $state->color())
                    ->icon(fn (MailSource $state): string => $state->icon()),
                TextColumn::make('sourceUser.name')
                    ->label(__('mail-log.source_user'))
                    ->placeholder(__('mail-log.source_user_placeholder'))
                    ->searchable(),
            ])
            ->filters([
                Filter::make('recipient')
                    ->schema([
                        TextInput::make('recipient')
                            ->label(__('mail-log.recipient')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['recipient'] ?? null,
                            fn (Builder $query, string $value): Builder => $query->where('recipient', 'like', '%'.$value.'%'),
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['recipient'])) {
                            return null;
                        }

                        return __('mail-log.recipient').': '.$data['recipient'];
                    }),
                Filter::make('sent_at')
                    ->schema([
                        DatePicker::make('from')->label(__('mail-log.sent_from')),
                        DatePicker::make('until')->label(__('mail-log.sent_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['from']) && empty($data['until'])) {
                            return null;
                        }

                        $from = ! empty($data['from']) ? Carbon::parse($data['from'])->format(AppHelper::DATE_FORMAT) : '…';
                        $until = ! empty($data['until']) ? Carbon::parse($data['until'])->format(AppHelper::DATE_FORMAT) : '…';

                        return __('mail-log.sent_at').': '.$from.' – '.$until;
                    }),
                SelectFilter::make('mailable')
                    ->label(__('mail-log.mailable'))
                    ->options(fn (): array => MailLog::query()
                        ->whereNotNull('mailable')
                        ->distinct()
                        ->orderBy('mailable')
                        ->pluck('mailable', 'mailable')
                        ->map(fn (string $class): string => class_basename($class))
                        ->all()),
                SelectFilter::make('source_type')
                    ->label(__('mail-log.source'))
                    ->options(MailSource::enumArray()),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100, 'all']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailLogs::route('/'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }
}

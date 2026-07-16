<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use App\Shared\Helpers\AppHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SportServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportServices';

    protected static ?string $recordTitleAttribute = 'service_name_cz';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_services.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('sport-event.relation_services.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->columnSpanFull()->schema([
                    TextInput::make('service_name_cz')
                        ->label(__('sport-event.relation_services.name'))
                        ->required(),
                ])->columns(1),
                DateTimePicker::make('last_booking_date_time')
                    ->label(__('sport-event.relation_services.last_booking'))
                    ->required(),
                TextInput::make('unit_price')
                    ->label(__('sport-event.relation_services.unit_price'))
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('qty_available')
                    ->label(__('sport-event.relation_services.qty_available'))
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('qty_already_ordered')
                    ->label(__('sport-event.relation_services.qty_ordered'))
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_name_cz')
                    ->label(__('sport-event.relation_services.table.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_booking_date_time')
                    ->icon('heroicon-o-calendar')
                    ->label(__('sport-event.relation_services.table.last_booking'))
                    ->dateTime(AppHelper::DATE_TIME_FULL_FORMAT)
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(__('sport-event.relation_services.table.unit_price'))
                    ->sortable(),
                TextColumn::make('qty_available')
                    ->label(__('sport-event.relation_services.table.qty_available'))
                    ->sortable(),
                TextColumn::make('qty_already_ordered')
                    ->label(__('sport-event.relation_services.table.qty_remaining'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions(self::buttonCreateActionVisibility())
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function buttonCreateActionVisibility(): array
    {
        // TODO z recordu nejak vytahnout jestli je oris nebo ne a pak to skryt
        return [CreateAction::make()];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SportEventNewsRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventNews';

    protected static ?string $label = 'Novinky';

    protected static ?string $title = 'Novinky';

    protected static ?string $recordTitleAttribute = 'sport_event_id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('date')
                    ->label(__('sport-event.event_news.date'))
                    ->required(),
                Textarea::make('text')
                    ->label(__('sport-event.event_news.content'))
                    ->autosize()
                    ->required(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->icon('heroicon-o-calendar')
                    ->label(__('sport-event.event_news.date'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('text')
                    ->label(__('sport-event.event_news.content'))
                    ->searchable(),
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
        return [CreateAction::make()];
    }
}

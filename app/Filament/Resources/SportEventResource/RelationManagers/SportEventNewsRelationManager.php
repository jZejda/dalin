<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function buttonCreateActionVisibility(): array
    {
        return [Tables\Actions\CreateAction::make()];
    }
}

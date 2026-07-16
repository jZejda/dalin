<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use App\Shared\Helpers\AppHelper;
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
use Illuminate\Database\Eloquent\Model;

class SportEventNewsRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventNews';

    protected static ?string $recordTitleAttribute = 'sport_event_id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_news.title');
    }

    public static function getModelLabel(): string
    {
        return __('sport-event.relation_news.label');
    }

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
                    ->dateTime(AppHelper::DATE_TIME_FORMAT)
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

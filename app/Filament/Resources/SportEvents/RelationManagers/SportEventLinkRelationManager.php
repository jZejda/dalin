<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use App\Enums\SportEventLinkType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SportEventLinkRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventLinks';

    protected static ?string $recordTitleAttribute = 'name_cz';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_links.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('sport-event.relation_links.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_cz')
                    ->label(__('sport-event.relation_links.name_cz'))
                    ->required(),
                TextInput::make('name_en')
                    ->label(__('sport-event.relation_links.name_en'))
                    ->required(),
                TextInput::make('description_cz')
                    ->label(__('sport-event.relation_links.description_cz')),
                TextInput::make('description_en')
                    ->label(__('sport-event.relation_links.description_en')),
                Grid::make()->columnSpanFull()->schema([
                    TextInput::make('source_url')
                        ->label(__('sport-event.relation_links.source_url'))
                        ->url()
                        ->required(),
                ])->columns(1),
                Select::make('source_type')
                    ->label(__('sport-event.relation_links.source_type'))
                    ->required()
                    ->options(SportEventLinkType::enumArray()),
                Select::make('internal')
                    ->label(__('sport-event.relation_links.internal'))
                    ->required()
                    ->options([
                        0 => __('sport-event.relation_links.internal_external_option'),
                    ])
                    ->default(0)
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_cz')
                    ->label(__('sport-event.relation_links.table.name_cz'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label(__('sport-event.relation_links.table.name_en'))
                    ->sortable(),
                TextColumn::make('source_url')
                    ->label(__('sport-event.relation_links.table.source_url')),
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

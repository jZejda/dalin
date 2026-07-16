<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SportClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportClasses';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_classes.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('sport-event.relation_classes.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->columnSpanFull()->schema([
                    TextInput::make('name')
                        ->label(__('sport-event.relation_classes.name'))
                        ->required(),
                    Select::make('class_definition_id')
                        ->label(__('sport-event.relation_classes.class_definition'))
                        ->required()
                        ->options(SportClassDefinition::all()->pluck('class_definition_full_label', 'id'))
                        ->searchable(),
                ])->columns(2),
                TextInput::make('distance')
                    ->label(__('sport-event.relation_classes.distance'))
                    ->suffix('km')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('climbing')
                    ->label(__('sport-event.relation_classes.climbing'))
                    ->suffix('m')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('controls')
                    ->label(__('sport-event.relation_classes.controls'))
                    ->numeric()
                    ->minValue(0),
                TextInput::make('fee')
                    ->label(__('sport-event.relation_classes.fee'))
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('legs')
                    ->label(__('sport-event.relation_classes.legs'))
                    ->numeric()
                    ->integer()
                    ->minValue(1),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('sport-event.relation_classes.table.name'))
                    ->description(
                        fn (SportClass $record): string => $record->classDefinition->class_definition_fullLabel ?? ''
                    )
                    ->searchable(),
                TextColumn::make('oris_id')->label(__('sport-event.relation_classes.table.oris_id')),
                TextColumn::make('distance')->label(__('sport-event.relation_classes.table.distance')),
                TextColumn::make('climbing')->label(__('sport-event.relation_classes.table.climbing')),
                TextColumn::make('controls')->label(__('sport-event.relation_classes.table.controls')),
                TextColumn::make('fee')->label(__('sport-event.relation_classes.table.fee')),
                TextColumn::make('legs')->label(__('sport-event.relation_classes.table.legs')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

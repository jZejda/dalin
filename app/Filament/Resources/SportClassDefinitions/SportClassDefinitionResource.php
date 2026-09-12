<?php

namespace App\Filament\Resources\SportClassDefinitions;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Clusters\Config\ConfigCluster;
use App\Filament\Resources\SportClassDefinitions\Pages\ListSportClassDefinitions;
use App\Filament\Resources\SportClassDefinitions\Pages\CreateSportClassDefinition;
use App\Filament\Resources\SportClassDefinitions\Pages\EditSportClassDefinition;
use App\Models\SportClassDefinition;
use App\Models\SportList;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class SportClassDefinitionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SportClassDefinition::class;

    protected static ?string $cluster = ConfigCluster::class;

    protected static ?int $navigationSort = 100;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.race_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('sport-class-definition.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('sport-class-definition.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sport-class-definition.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([

                        TextInput::make('name')
                            ->label(__('sport-class-definition.form.name'))
                            ->required(),
                        Select::make('gender')
                            ->label(__('sport-class-definition.form.gender'))
                            ->options([
                                'F' => __('sport-class-definition.form.gender_female'),
                                'M' => __('sport-class-definition.form.gender_male'),
                                'A' => __('sport-class-definition.form.gender_all'),
                            ])
                            ->required(),

                        TextInput::make('age_from')
                            ->label(__('sport-class-definition.form.age_from'))
                            ->required(),
                        TextInput::make('age_to')
                            ->label(__('sport-class-definition.form.age_to'))
                            ->required(),

                        Select::make('sport_id')
                            ->label(__('sport-class-definition.form.sport'))
                            ->options(SportList::all()->pluck('short_name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('oris_id')
                            ->label(__('sport-class-definition.form.oris_id'))
                            ->disabled(true),
                    ])
                    ->columns(2)
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 12
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->description(fn (SportClassDefinition $record): string => $record->class_definition_fullLabel ?? ''),
                TextColumn::make('age_from')
                    ->label(__('sport-class-definition.table.age_from'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('age_to')
                    ->label(__('sport-class-definition.table.age_to'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('gender')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sport.short_name')
                    ->label(__('sport-class-definition.table.sport'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('oris_id')
                    ->label(__('sport-class-definition.table.oris_id')),
            ])
            ->defaultPaginationPageOption(25)
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSportClassDefinitions::route('/'),
            'create' => CreateSportClassDefinition::route('/create'),
            'edit' => EditSportClassDefinition::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}

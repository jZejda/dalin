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

class SportEventLinkRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventLinks';

    protected static ?string $label = 'Odkazy';

    protected static ?string $title = 'Odkazy';

    protected static ?string $recordTitleAttribute = 'name_cz';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_cz')
                    ->label('Název odkazu česky')
                    ->required(),
                TextInput::make('name_en')
                    ->label('Název odkazu anglicky')
                    ->required(),
                TextInput::make('description_cz')
                    ->label('Popis odkazu česky'),
                TextInput::make('description_en')
                    ->label('Popis odkazu anglicky'),
                Grid::make()->schema([
                    TextInput::make('source_url')
                        ->label('URL odkazu')
                        ->url()
                        ->required(),
                ])->columns(1),
                Select::make('source_type')
                    ->label('Typ odkazu')
                    ->required()
                    ->options(SportEventLinkType::enumArray()),
                Select::make('internal')
                    ->label('Odkaz')
                    ->required()
                    ->options([
                        0 => 'Externí odkaz',
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
                    ->label('Odkaz česky')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label('Odkaz anglicky')
                    ->sortable(),
                TextColumn::make('source_url')
                    ->label('Odkaz'),
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

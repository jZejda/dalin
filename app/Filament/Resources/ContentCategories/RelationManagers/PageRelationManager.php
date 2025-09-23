<?php

namespace App\Filament\Resources\ContentCategories\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DissociateAction;
use App\Models\ContentCategory;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class PageRelationManager extends RelationManager
{
    protected static string $relationship = 'page';

    protected static ?string $label = 'Stránka(y)';

    protected static ?string $title = 'Stránka(y)';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('content_category_id')
                    ->label('Kategorie')
                    ->options(ContentCategory::all()->pluck('title', 'id'))
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')->label('Autor')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            //    Tables\Actions\CreateAction::make(),
//                Tables\Actions\AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
//                Tables\Actions\DissociateBulkAction::make(),
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

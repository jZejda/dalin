<?php

namespace App\Filament\Resources\ContentCategoryResource\RelationManagers;

use App\Models\ContentCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class PageRelationManager extends RelationManager
{
    protected static string $relationship = 'page';

    protected static ?string $label = 'Stránka(y)';

    protected static ?string $title = 'Stránka(y)';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Autor')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DissociateBulkAction::make(),
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

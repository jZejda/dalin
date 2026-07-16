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
use Illuminate\Database\Eloquent\Model;

class PageRelationManager extends RelationManager
{
    protected static string $relationship = 'page';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('content.page.relation.title');
    }

    public static function getModelLabel(): string
    {
        return __('content.page.relation.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('content_category_id')
                    ->label(__('content.page.form.category'))
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
                TextColumn::make('user.name')->label(__('content.page.table.author'))
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

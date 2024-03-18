<?php

declare(strict_types=1);

namespace App\Livewire\Backend;

use App\Models\UserRaceProfile;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class UserRaceProfileTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithRecord;

    public Model|int|string|null $record;

    public function table(Table $table): Table
    {
        return $table
            ->query(UserRaceProfile::query())
            ->columns([
                TextColumn::make('reg_number')
                    ->label(__('user-race-profile.table.reg_number'))
                    ->sortable()
                    ->searchable()
                    ->size(TextColumnSize::Large)
                    ->color(function (UserRaceProfile $model): string {
                        if(!$model->active) {
                            return 'danger';
                        }
                        return 'default';
                    })
                    ->description(function (UserRaceProfile $model): ?string {
                        if(!$model->active) {
                            return __('user-race-profile.table.active_until') . ': ' . $model->active_until?->format('d.m.Y');
                        }
                        return null;
                    }),
                TextColumn::make('first_name')
                    ->label(__('user-race-profile.table.first_name'))
                    ->size(TextColumnSize::Large)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label(__('user-race-profile.table.last_name'))
                    ->size(TextColumnSize::Large)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('user-race-profile.table.email'))
                    ->icon('heroicon-o-envelope')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('user-race-profile.table.phone'))
                    ->icon('heroicon-o-phone')
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('active')
                    ->label(__('user-race-profile.table.active'))
                    ->hidden(!Auth::user()?->hasRole('super_admin')),
                TextColumn::make('created_at')
                    ->label(__('user-race-profile.table.created_at'))
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('active_until')
                    ->label(__('user-race-profile.table.active_until'))
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->badge()
                    ->icon('heroicon-o-user')
                    ->label(__('user-race-profile.table.user-name'))
                    ->colors(function (UserRaceProfile $record): array {
                        if ($record->user?->isActive()) {
                            return ['success'];
                        }
                        return ['danger'];
                    })
                    ->searchable()
                    ->searchable(),
            ])
            ->defaultSort('last_name', 'asc')
            ->defaultPaginationPageOption(25)
            ->filters([
                TernaryFilter::make('active')
                    ->label(__('user-race-profile.table_filter.registrations'))
                    ->placeholder(__('user-race-profile.table_filter.all_registrations'))
                    ->trueLabel(__('user-race-profile.table_filter.active_registrations'))
                    ->falseLabel(__('user-race-profile.table_filter.disable_registrations'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('active', '=', 1),
                        false: fn (Builder $query) => $query->where('active', '=', 0),
                        blank: fn (Builder $query) => $query,
                    )
                    ->default(),
            ])
            ->actions([
//                Action::make('edit')
//                    ->url(fn (UserRaceProfile $record): string => '/')
//                    ->openUrlInNewTab()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->headerActions([
                //
            ]);
    }

    public function render(): View
    {
        return view('livewire.backend.user-race-profile');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\Actions\DeleteEntryAction;
use App\Filament\Resources\SportEvents\Pages\Actions\UpdateEntryAction;
use App\Filament\Resources\SportEvents\Pages\Table\EntriesTableColumns;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class EntryList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public SportEvent $sportEvent;

    #[On('entry-created')]
    public function refreshList(): void
    {
        // Re-render only, the table query picks up the new entry.
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(UserEntry::query()->where('sport_event_id', '=', $this->sportEvent->id))
            ->columns(EntriesTableColumns::make($this->sportEvent))
            ->recordClasses('!py-0')
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'asc')
            ->filters([
                SelectFilter::make('entry_status')
                    ->options(EntryStatus::enumArray())
                    ->multiple()
                    ->default([EntryStatus::Create->value, EntryStatus::Edit->value]),
            ])
            ->recordActions([
                (new UpdateEntryAction())->make(),
                (new DeleteEntryAction())->make(),
            ]);
    }

    public function render(): View
    {
        /** @var view-string $template */
        $template = 'livewire.sport-event.entry-list';

        return view($template);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Pages;

use App\Enums\SiSyncStatus;
use App\Filament\Clusters\Config\ConfigCluster;
use App\Models\UserRaceProfile;
use App\Services\SiChips\OrisClubMember;
use App\Services\SiChips\OrisSiClient;
use App\Services\SiChips\OrisSiException;
use App\Services\SiChips\SiChipComparator;
use App\Services\SiChips\SiChipComparison;
use App\Services\SiChips\SiChipData;
use App\Services\SiChips\SiChipImporter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

/**
 * Admin overview comparing the SI chips of race profiles in Dalin with the club members in ORIS.
 */
class SiChipOverview extends Page implements HasForms, HasTable
{
    use HasPageShield;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $cluster = ConfigCluster::class;

    protected static ?string $slug = 'si-chips';

    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::Nfc;

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.clusters.config.pages.si-chip-overview';

    /** @var array<int, SiChipComparison>|null */
    protected ?array $comparisons = null;

    protected ?string $orisError = null;

    public static function getNavigationLabel(): string
    {
        return __('si-chip.overview.navigation_label');
    }

    public function getTitle(): string
    {
        return __('si-chip.overview.title');
    }

    public function mount(): void
    {
        $this->comparisons();

        if ($this->orisError !== null) {
            Notification::make()
                ->title(__('si-chip.overview.load_failed'))
                ->body($this->orisError)
                ->danger()
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('si-chip.overview.actions.refresh.label'))
                ->icon(LucideIcon::RefreshCw)
                ->color('gray')
                ->action(function (): void {
                    app(OrisSiClient::class)->forget();
                    $this->comparisons = null;
                    $this->comparisons();

                    if ($this->orisError !== null) {
                        Notification::make()
                            ->title(__('si-chip.overview.load_failed'))
                            ->body($this->orisError)
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title(__('si-chip.overview.actions.refresh.notification_title'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(UserRaceProfile::query()->with('chips'))
            ->columns([
                TextColumn::make('user_race_full_name')
                    ->label(__('si-chip.overview.columns.member'))
                    ->state(fn (UserRaceProfile $record): string => $record->last_name.' '.$record->first_name)
                    ->description(fn (UserRaceProfile $record): string => $record->reg_number)
                    ->searchable(['first_name', 'last_name', 'reg_number'])
                    ->sortable(['last_name', 'first_name']),
                TextColumn::make('dalin_chips')
                    ->label(__('si-chip.overview.columns.dalin'))
                    ->state(fn (UserRaceProfile $record): array => $this->chipLabels($this->comparisonFor($record)->dalinChips))
                    ->listWithLineBreaks()
                    ->placeholder(__('si-chip.overview.columns.none')),
                TextColumn::make('oris_chips')
                    ->label(__('si-chip.overview.columns.oris'))
                    ->state(fn (UserRaceProfile $record): array => $this->chipLabels($this->comparisonFor($record)->orisChips))
                    ->listWithLineBreaks()
                    ->placeholder(__('si-chip.overview.columns.none')),
                TextColumn::make('status')
                    ->label(__('si-chip.overview.columns.status'))
                    ->state(fn (UserRaceProfile $record): SiSyncStatus => $this->comparisonFor($record)->status)
                    ->badge(),
            ])
            ->defaultSort('last_name')
            ->filters([
                Filter::make('active')
                    ->label(__('si-chip.overview.filters.active'))
                    ->toggle()
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->where('active', true)),
                Filter::make('only_differences')
                    ->label(__('si-chip.overview.filters.only_differences'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereIn('id', $this->profileIdsWithStatus(
                        array_filter(SiSyncStatus::cases(), fn (SiSyncStatus $status): bool => $status->isDifference())
                    ))),
                SelectFilter::make('status')
                    ->label(__('si-chip.overview.filters.status'))
                    ->options(SiSyncStatus::class)
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if (! is_string($value) || $value === '') {
                            return $query;
                        }

                        $status = SiSyncStatus::tryFrom($value);

                        return $status === null ? $query : $query->whereIn('id', $this->profileIdsWithStatus([$status]));
                    }),
            ])
            ->recordActions([
                Action::make('pull_from_oris')
                    ->label(__('si-chip.overview.actions.pull.label'))
                    ->icon(LucideIcon::ArrowLeftRight)
                    ->color('gray')
                    ->visible(fn (UserRaceProfile $record): bool => $this->comparisonFor($record)->canPullFromOris())
                    ->requiresConfirmation()
                    ->modalHeading(__('si-chip.overview.actions.pull.modal_heading'))
                    ->modalDescription(function (UserRaceProfile $record): string {
                        $comparison = $this->comparisonFor($record);

                        return __('si-chip.overview.actions.pull.modal_description', [
                            'name' => $record->user_race_full_name,
                            'oris' => implode(', ', $this->chipLabels($comparison->orisChips)),
                            'dalin' => implode(', ', $this->chipLabels($comparison->dalinChips)) ?: __('si-chip.overview.columns.none'),
                        ]);
                    })
                    ->modalSubmitActionLabel(__('si-chip.overview.actions.pull.modal_submit'))
                    ->action(function (UserRaceProfile $record): void {
                        $member = $this->comparisonFor($record)->orisMember;

                        if (! $member instanceof OrisClubMember) {
                            return;
                        }

                        try {
                            app(SiChipImporter::class)->pullFromOris($record, $member);
                        } catch (Throwable $e) {
                            report($e);

                            Notification::make()->title(__('si-chip.errors.pull_failed'))->danger()->send();

                            return;
                        }

                        $this->comparisons = null;

                        Notification::make()
                            ->title(__('si-chip.overview.actions.pull.notification_title'))
                            ->body(__('si-chip.overview.actions.pull.notification_body', ['name' => $record->user_race_full_name]))
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading(__('si-chip.overview.empty_heading'))
            ->emptyStateIcon(LucideIcon::Nfc);
    }

    /**
     * @param  list<SiChipData>  $chips
     * @return list<string>
     */
    private function chipLabels(array $chips): array
    {
        return array_map(fn (SiChipData $chip): string => $chip->label(), $chips);
    }

    private function comparisonFor(UserRaceProfile $profile): SiChipComparison
    {
        return $this->comparisons()[$profile->id] ?? (new SiChipComparator())->compare($profile, null);
    }

    /**
     * @param  iterable<SiSyncStatus>  $statuses
     * @return list<int>
     */
    private function profileIdsWithStatus(iterable $statuses): array
    {
        $wanted = [];
        foreach ($statuses as $status) {
            $wanted[$status->value] = true;
        }

        $ids = [];
        foreach ($this->comparisons() as $profileId => $comparison) {
            if (isset($wanted[$comparison->status->value])) {
                $ids[] = $profileId;
            }
        }

        return $ids;
    }

    /**
     * Comparison of every race profile, keyed by profile id. ORIS is read once per request
     * (the member list itself is cached in OrisSiClient).
     *
     * @return array<int, SiChipComparison>
     */
    private function comparisons(): array
    {
        if ($this->comparisons !== null) {
            return $this->comparisons;
        }

        $this->orisError = null;

        try {
            $members = app(OrisSiClient::class)->clubMembers();
        } catch (OrisSiException $e) {
            $members = null;
            $this->orisError = $e->getMessage();
        }

        $comparator = new SiChipComparator();
        $this->comparisons = [];

        foreach (UserRaceProfile::query()->with('chips')->get() as $profile) {
            $this->comparisons[$profile->id] = $comparator->compare($profile, $members);
        }

        return $this->comparisons;
    }
}

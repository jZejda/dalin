<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages;

use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use App\Models\UserRaceProfile;
use App\Services\SiChips\OrisSiClient;
use App\Services\SiChips\OrisSiException;
use App\Services\SiChips\SiChipComparator;
use App\Services\SiChips\SiChipComparison;
use App\Services\SiChips\SiChipData;
use App\Services\SiChips\SiChipPusher;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditUserRaceProfile extends EditRecord
{
    protected static string $resource = UserRaceProfileResource::class;

    private ?SiChipComparison $siComparison = null;

    private bool $siComparisonLoaded = false;

    private ?string $chipsBeforeSave = null;

    protected function getHeaderActions(): array
    {
        return [
            $this->syncSiToOrisAction(),
            DeleteAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        $comparison = $this->siComparison();

        if (! $comparison instanceof SiChipComparison || ! $comparison->status->isDifference()) {
            return null;
        }

        return __('si-chip.profile.differs_notice', [
            'oris' => $this->chipLabels($comparison->orisChips),
            'dalin' => $this->chipLabels($comparison->dalinChips),
        ]);
    }

    protected function beforeSave(): void
    {
        $this->chipsBeforeSave = $this->chipSignature();
    }

    /**
     * After a change of the chips offer to bring ORIS up to date as well.
     */
    protected function afterSave(): void
    {
        $profile = $this->profile();
        $profile->unsetRelation('chips');

        // Saving e.g. a phone number must not cost an ORIS round trip.
        if ($this->chipSignature() === $this->chipsBeforeSave) {
            return;
        }

        // The member is deciding right now, so show what ORIS really holds, not a cached list.
        app(OrisSiClient::class)->forget();
        $this->siComparisonLoaded = false;

        if ($this->siComparison()?->canPushToOris() === true) {
            $this->mountAction('sync_si_to_oris');
        }
    }

    private function syncSiToOrisAction(): Action
    {
        return Action::make('sync_si_to_oris')
            ->label(__('si-chip.profile.sync_action'))
            ->icon(LucideIcon::Upload)
            ->color('gray')
            ->visible(fn (): bool => $this->siComparison()?->canPushToOris() === true)
            ->requiresConfirmation()
            ->modalHeading(__('si-chip.profile.modal_heading'))
            ->modalDescription(fn (): string => __('si-chip.profile.modal_description', [
                'oris' => $this->chipLabels($this->siComparison()->orisChips ?? []) ?: __('si-chip.overview.columns.none'),
                'dalin' => $this->chipLabels($this->siComparison()->dalinChips ?? []),
            ]))
            ->modalSubmitActionLabel(__('si-chip.profile.modal_submit'))
            ->modalCancelActionLabel(__('si-chip.profile.modal_cancel'))
            ->action(function (): void {
                try {
                    app(SiChipPusher::class)->push($this->profile());
                } catch (OrisSiException $e) {
                    Notification::make()
                        ->title(__('si-chip.profile.notification_failed'))
                        ->body($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                $this->siComparisonLoaded = false;

                Notification::make()
                    ->title(__('si-chip.profile.notification_title'))
                    ->success()
                    ->send();
            });
    }

    /**
     * Comparison with ORIS, or null when ORIS is not available / the profile is not linked to it.
     */
    private function siComparison(): ?SiChipComparison
    {
        if ($this->siComparisonLoaded) {
            return $this->siComparison;
        }

        $this->siComparisonLoaded = true;
        $this->siComparison = null;

        if (! app(OrisSiClient::class)->isConfigured()) {
            return null;
        }

        try {
            $comparison = app(SiChipPusher::class)->compare($this->profile());
        } catch (OrisSiException) {
            return null;
        } catch (Throwable $e) {
            // The ORIS sync is an optional extra - it must never break the profile page.
            report($e);

            return null;
        }

        return $this->siComparison = $comparison->orisMember === null ? null : $comparison;
    }

    /**
     * Fingerprint of the chips as Dalin holds them (reads the relation again).
     */
    private function chipSignature(): string
    {
        $profile = $this->profile();
        $profile->unsetRelation('chips');

        return implode('|', array_map(
            fn (SiChipData $chip): string => $chip->key(),
            (new SiChipComparator())->dalinChips($profile),
        ));
    }

    private function profile(): UserRaceProfile
    {
        /** @var UserRaceProfile $profile */
        $profile = $this->getRecord();

        return $profile;
    }

    /**
     * @param  list<SiChipData>  $chips
     */
    private function chipLabels(array $chips): string
    {
        return implode(', ', array_map(fn (SiChipData $chip): string => $chip->label(), $chips));
    }
}

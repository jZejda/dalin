<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\MarketOfferStatus;
use App\Models\AppSetting;
use App\Models\MarketProduct;
use App\Models\User;
use App\Services\MarketplaceService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class MarketplaceList extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 103;

    protected string $view = 'filament.pages.marketplace-list';

    public string $activeTab = 'active';

    public static function canAccess(): bool
    {
        return Auth::check() && AppSetting::isMarketplaceModuleEnabled();
    }

    public static function getNavigationLabel(): string
    {
        return __('marketplace.marketplace_title');
    }

    public function getTitle(): string
    {
        return __('marketplace.marketplace_title');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MarketProduct::query()
                    ->with(['marketOffer.user'])
                    ->whereHas('marketOffer', function (Builder $query): void {
                        if ($this->activeTab === 'active') {
                            $query->where('status', '=', MarketOfferStatus::Active)
                                ->where('closes_at', '>', now());
                        } else {
                            $query->where('status', '!=', MarketOfferStatus::Active)
                                ->orWhere('closes_at', '<=', now());
                        }
                    })
            )
            ->defaultGroup(
                Group::make('market_offer_id')
                    ->label(__('marketplace.offer'))
                    ->getTitleFromRecordUsing(fn (MarketProduct $record): string => $record->marketOffer->title ?? '')
                    ->getDescriptionFromRecordUsing(fn (MarketProduct $record): string => $this->offerGroupDescription($record))
                    ->orderQueryUsing(fn (Builder $query): Builder => $query->orderByDesc('market_offer_id')),
            )
            ->groupingSettingsHidden()
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('marketplace.product_image'))
                    ->collection(MarketProduct::MEDIA_COLLECTION_IMAGE)
                    ->imageSize(64),
                TextColumn::make('name')
                    ->label(__('marketplace.product'))
                    ->weight('bold')
                    ->description(fn (MarketProduct $record): ?string => $record->description)
                    ->url(fn (MarketProduct $record): ?string => $record->url, shouldOpenInNewTab: true)
                    ->searchable(),
                TextColumn::make('unit_price')
                    ->label(__('marketplace.unit_price'))
                    ->state(fn (MarketProduct $record): string => $record->isFree()
                        ? __('marketplace.free_badge')
                        : number_format($record->unit_price, 2, ',', ' ').' '.__('marketplace.price_suffix'))
                    ->badge(fn (MarketProduct $record): bool => $record->isFree())
                    ->color(fn (MarketProduct $record): ?string => $record->isFree() ? 'success' : null),
                TextColumn::make('payment_method')
                    ->label(__('marketplace.payment_method'))
                    ->badge(),
                TextColumn::make('qty_remaining')
                    ->label(__('marketplace.qty_remaining'))
                    ->state(function (MarketProduct $record): string {
                        $remaining = $record->qtyRemaining();

                        if ($remaining === null) {
                            return __('marketplace.qty_unlimited');
                        }

                        return $remaining === 0 ? __('marketplace.sold_out') : (string) $remaining;
                    })
                    ->badge()
                    ->color(function (MarketProduct $record): string {
                        $remaining = $record->qtyRemaining();

                        if ($remaining === null) {
                            return 'gray';
                        }

                        return $remaining === 0 ? 'danger' : 'success';
                    }),
            ])
            ->recordActions([
                $this->orderAction(),
            ])
            ->emptyStateHeading($this->activeTab === 'active'
                ? __('marketplace.empty_marketplace')
                : __('marketplace.empty_marketplace_past'))
            ->emptyStateDescription(function (): ?string {
                if ($this->activeTab !== 'active') {
                    return null;
                }

                return __('marketplace.empty_marketplace_description');
            })
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->paginated([10, 25, 50]);
    }

    private function orderAction(): Action
    {
        return Action::make('order')
            ->label(__('marketplace.order_action'))
            ->icon('heroicon-o-shopping-cart')
            ->color('success')
            ->button()
            ->visible(function (MarketProduct $record): bool {
                $offer = $record->marketOffer;

                return $this->activeTab === 'active'
                    && $offer !== null
                    && $offer->isOpenForOrders()
                    && $offer->user_id !== Auth::id()
                    && ! $record->isSoldOut();
            })
            ->modalHeading(fn (MarketProduct $record): string => __('marketplace.order_action').': '.$record->name)
            ->modalDescription(fn (MarketProduct $record): string => $record->isFree()
                ? __('marketplace.free_badge').' · '.$record->payment_method->getLabel()
                : number_format($record->unit_price, 2, ',', ' ').' '.__('marketplace.price_suffix').'/ks · '.$record->payment_method->getLabel())
            ->schema(fn (MarketProduct $record): array => [
                TextInput::make('qty')
                    ->label(__('marketplace.order_qty'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($record->qtyRemaining())
                    ->default(1)
                    ->required()
                    ->live(),
                Placeholder::make('total')
                    ->label(__('marketplace.order_total'))
                    ->content(function (Get $get) use ($record): string {
                        if ($record->isFree()) {
                            return __('marketplace.free_badge');
                        }

                        $qty = max(0, (int) $get('qty'));

                        return number_format($qty * $record->unit_price, 2, ',', ' ').' '.__('marketplace.price_suffix');
                    }),
                Textarea::make('note')
                    ->label(__('marketplace.order_note'))
                    ->rows(2)
                    ->maxLength(255),
            ])
            ->action(function (MarketProduct $record, array $data): void {
                /** @var User $user */
                $user = Auth::user();

                try {
                    app(MarketplaceService::class)->order(
                        $record,
                        $user,
                        (int) $data['qty'],
                        isset($data['note']) && $data['note'] !== '' ? (string) $data['note'] : null,
                    );
                } catch (RuntimeException $exception) {
                    Notification::make()
                        ->title($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('marketplace.order_created_notification'))
                    ->success()
                    ->send();
            });
    }

    private function offerGroupDescription(MarketProduct $record): string
    {
        $offer = $record->marketOffer;

        if ($offer === null) {
            return '';
        }

        $parts = [
            __('marketplace.offer_author').': '.($offer->user->name ?? ''),
            __('marketplace.offer_closes_at').' '.$offer->closes_at->format('j. n. Y H:i'),
        ];

        if ($offer->is_club_offer) {
            $parts[] = __('marketplace.club_offer_badge');
        }

        if ($offer->description !== null && $offer->description !== '') {
            $parts[] = $offer->description;
        }

        return implode(' · ', $parts);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\AppRoles;
use App\Enums\MarketOfferStatus;
use App\Enums\MarketPaymentMethod;
use App\Models\AppSetting;
use App\Models\MarketOffer;
use App\Models\MarketProduct;
use App\Models\User;
use App\Services\MarketplaceService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class MyMarketOfferList extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.my-market-offer-list';

    public static function canAccess(): bool
    {
        return Auth::check() && AppSetting::isMarketplaceModuleEnabled();
    }

    public static function getNavigationLabel(): string
    {
        return __('marketplace.my_offers_title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('marketplace.navigation_group');
    }

    public function getTitle(): string
    {
        return __('marketplace.my_offers_title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MarketOffer::query()
                    ->where(function (Builder $query): void {
                        $query->where('user_id', '=', Auth::id());

                        // Pokladník vidí i cizí oddílové nabídky, aby je mohl rozúčtovat.
                        if ($this->canBillClubOffers()) {
                            $query->orWhere('is_club_offer', '=', true);
                        }
                    })
                    ->withCount(['products', 'orders'])
            )
            ->columns([
                TextColumn::make('title')
                    ->label(__('marketplace.offer_title'))
                    ->weight('bold')
                    ->description(fn (MarketOffer $record): ?string => $record->description)
                    ->searchable(),
                TextColumn::make('is_club_offer')
                    ->label('')
                    ->badge()
                    ->state(function (MarketOffer $record): ?string {
                        if (! $record->is_club_offer) {
                            return null;
                        }

                        return __('marketplace.club_offer_badge');
                    })
                    ->color('info'),
                TextColumn::make('user.name')
                    ->label(__('marketplace.offer_author_column'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(__('marketplace.offer_status'))
                    ->badge(),
                TextColumn::make('closes_at')
                    ->label(__('marketplace.offer_closes_at'))
                    ->dateTime('j. n. Y H:i'),
                TextColumn::make('products_count')
                    ->label(__('marketplace.products_count')),
                TextColumn::make('orders_count')
                    ->label(__('marketplace.orders_count')),
                TextColumn::make('created_at')
                    ->label('Vytvořeno')
                    ->dateTime('j. n. Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                $this->offerCreateAction(),
            ])
            ->recordActions([
                ActionGroup::make([
                    $this->showOrdersAction(),
                    EditAction::make()
                        ->modalHeading(__('marketplace.edit_offer'))
                        ->slideOver()
                        ->schema($this->offerFormComponents())
                        ->visible(fn (MarketOffer $record): bool => $record->user_id === Auth::id()
                            && $record->status !== MarketOfferStatus::Billed),
                    $this->sendAnnouncementAction(),
                    $this->closeOfferAction(),
                    $this->billOfferAction(),
                ]),
            ])
            ->emptyStateHeading(__('marketplace.empty_my_offers'))
            ->emptyStateDescription(__('marketplace.empty_my_offers_description'))
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateActions([
                $this->offerCreateAction(),
            ])
            ->paginated([10, 25, 50]);
    }

    private function offerCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->model(MarketOffer::class)
            ->label(__('marketplace.create_offer'))
            ->modalHeading(__('marketplace.create_offer'))
            ->icon('heroicon-o-plus')
            ->slideOver()
            ->schema($this->offerFormComponents())
            ->mutateDataUsing(function (array $data): array {
                $data['user_id'] = Auth::id();

                return $data;
            });
    }

    private function showOrdersAction(): Action
    {
        return Action::make('showOrders')
            ->label(__('marketplace.show_orders'))
            ->icon('heroicon-o-clipboard-document-list')
            ->modalHeading(fn (MarketOffer $record): string => __('marketplace.show_orders').': '.$record->title)
            ->modalContent(fn (MarketOffer $record) => view(
                'filament.pages.market-offer-orders',
                ['offer' => $record->load(['products.orders.user'])],
            ))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Zavřít');
    }

    private function sendAnnouncementAction(): Action
    {
        return Action::make('sendAnnouncement')
            ->label(__('marketplace.send_announcement'))
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->modalHeading(__('marketplace.send_announcement'))
            ->modalDescription(__('marketplace.send_announcement_confirmation'))
            ->visible(fn (MarketOffer $record): bool => $record->user_id === Auth::id()
                && $record->status === MarketOfferStatus::Active)
            ->action(function (MarketOffer $record): void {
                $count = app(MarketplaceService::class)->announceOffer($record);

                Notification::make()
                    ->title(__('marketplace.announcement_sent_notification'))
                    ->body(__('marketplace.announcement_sent_notification_body', ['count' => $count]))
                    ->success()
                    ->send();
            });
    }

    private function billOfferAction(): Action
    {
        return Action::make('billOffer')
            ->label(__('marketplace.bill_offer'))
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('marketplace.bill_offer'))
            ->modalDescription(__('marketplace.bill_offer_confirmation'))
            ->visible(fn (MarketOffer $record): bool => $record->status === MarketOfferStatus::Closed
                && $this->canBillOffer($record))
            ->action(function (MarketOffer $record): void {
                /** @var User $user */
                $user = Auth::user();

                try {
                    $count = app(MarketplaceService::class)->billOffer($record, $user);
                } catch (RuntimeException $exception) {
                    Notification::make()
                        ->title($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('marketplace.offer_billed_notification'))
                    ->body(__('marketplace.offer_billed_notification_body', ['count' => $count]))
                    ->success()
                    ->send();
            });
    }

    private function closeOfferAction(): Action
    {
        return Action::make('closeOffer')
            ->label(__('marketplace.close_offer'))
            ->icon('heroicon-o-lock-closed')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('marketplace.close_offer'))
            ->modalDescription(__('marketplace.close_offer_confirmation'))
            ->visible(fn (MarketOffer $record): bool => $record->user_id === Auth::id()
                && $record->status === MarketOfferStatus::Active)
            ->action(function (MarketOffer $record): void {
                try {
                    app(MarketplaceService::class)->closeOffer($record);
                } catch (RuntimeException $exception) {
                    Notification::make()
                        ->title($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('marketplace.offer_closed_notification'))
                    ->success()
                    ->send();
            });
    }

    /**
     * Formulář nabídky s repeaterem produktů. U produktu s objednávkami
     * je cena a způsob úhrady uzamčen a položky nelze mazat.
     *
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    private function offerFormComponents(): array
    {
        return [
            Section::make(__('marketplace.offer'))
                ->schema([
                    TextInput::make('title')
                        ->label(__('marketplace.offer_title'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label(__('marketplace.offer_description'))
                        ->rows(3),
                    DateTimePicker::make('closes_at')
                        ->label(__('marketplace.offer_closes_at'))
                        ->required()
                        ->minDate(now()),
                    Toggle::make('is_club_offer')
                        ->label(__('marketplace.club_offer'))
                        ->helperText(__('marketplace.club_offer_helper'))
                        ->visible(fn (): bool => $this->canCreateClubOffer()),
                ]),
            Section::make(__('marketplace.products'))
                ->schema([
                    Repeater::make('products')
                        ->relationship()
                        ->hiddenLabel()
                        ->minItems(1)
                        ->columns(2)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => is_string($state['name'] ?? null) ? $state['name'] : null)
                        ->addActionLabel(__('marketplace.product'))
                        ->deletable(fn (?MarketOffer $record): bool => $record === null || ! $record->orders()->exists())
                        ->helperText(__('marketplace.products_locked_helper'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('marketplace.product_name'))
                                ->required()
                                ->maxLength(255),
                            TextInput::make('url')
                                ->label(__('marketplace.product_url'))
                                ->url()
                                ->maxLength(255),
                            Textarea::make('description')
                                ->label(__('marketplace.product_description'))
                                ->rows(2)
                                ->columnSpanFull(),
                            TextInput::make('unit_price')
                                ->label(__('marketplace.unit_price'))
                                ->helperText(__('marketplace.unit_price_helper'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required()
                                ->suffix(__('marketplace.price_suffix'))
                                ->disabled(fn (?MarketProduct $record): bool => $this->productIsLocked($record)),
                            Select::make('payment_method')
                                ->label(__('marketplace.payment_method'))
                                ->options(MarketPaymentMethod::enumArray())
                                ->default(MarketPaymentMethod::CreditCharge->value)
                                ->required()
                                ->disabled(fn (?MarketProduct $record): bool => $this->productIsLocked($record)),
                            TextInput::make('qty_available')
                                ->label(__('marketplace.qty_available'))
                                ->helperText(__('marketplace.qty_available_helper'))
                                ->numeric()
                                ->minValue(1),
                            SpatieMediaLibraryFileUpload::make('image')
                                ->label(__('marketplace.product_image'))
                                ->collection(MarketProduct::MEDIA_COLLECTION_IMAGE)
                                ->image()
                                ->maxSize(4096),
                        ]),
                ]),
        ];
    }

    private function productIsLocked(?MarketProduct $product): bool
    {
        return $product !== null && $product->orders()->exists();
    }

    private function canCreateClubOffer(): bool
    {
        return Auth::user()?->hasRole([
            AppRoles::SuperAdmin,
            AppRoles::ClubAdmin,
            AppRoles::EventMaster,
        ]) ?? false;
    }

    /**
     * Oddílovou nabídku rozúčtuje pokladník, osobní její zadavatel.
     */
    private function canBillOffer(MarketOffer $offer): bool
    {
        if ($offer->is_club_offer) {
            return $this->canBillClubOffers();
        }

        return $offer->user_id === Auth::id();
    }

    private function canBillClubOffers(): bool
    {
        return Auth::user()?->hasRole([
            AppRoles::SuperAdmin,
            AppRoles::BillingSpecialist,
        ]) ?? false;
    }
}

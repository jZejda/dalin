<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Pages;

use App\Enums\MarketOrderStatus;
use App\Filament\Clusters\Other\OtherCluster;
use App\Filament\Pages\MarketplaceList;
use App\Models\AppSetting;
use App\Models\MarketOrder;
use App\Services\MarketplaceService;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class MyMarketOrderList extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::ShoppingCart;

    protected static ?string $cluster = OtherCluster::class;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.clusters.other.pages.my-market-order-list';

    public static function canAccess(): bool
    {
        return Auth::check() && AppSetting::isMarketplaceModuleEnabled();
    }

    public static function getNavigationLabel(): string
    {
        return __('marketplace.my_orders_title');
    }

    public function getTitle(): string
    {
        return __('marketplace.my_orders_title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MarketOrder::query()
                    ->where('user_id', '=', Auth::id())
                    ->with(['marketProduct.marketOffer.user'])
            )
            ->columns([
                TextColumn::make('marketProduct.name')
                    ->label(__('marketplace.product'))
                    ->weight('bold')
                    ->description(fn (MarketOrder $record): string => __('marketplace.offer').': '
                        .($record->marketProduct->marketOffer->title ?? '')
                        .' — '.($record->marketProduct->marketOffer->user->name ?? ''))
                    ->searchable(),
                TextColumn::make('qty')
                    ->label(__('marketplace.order_qty')),
                TextColumn::make('unit_price')
                    ->label(__('marketplace.unit_price'))
                    ->state(fn (MarketOrder $record): string => $record->unit_price === 0.0
                        ? __('marketplace.free_badge')
                        : number_format($record->unit_price, 2, ',', ' ').' '.__('marketplace.price_suffix')),
                TextColumn::make('total')
                    ->label(__('marketplace.order_total'))
                    ->weight('bold')
                    ->state(fn (MarketOrder $record): string => $record->unit_price === 0.0
                        ? '—'
                        : number_format($record->totalAmount(), 2, ',', ' ').' '.__('marketplace.price_suffix')),
                TextColumn::make('marketProduct.payment_method')
                    ->label(__('marketplace.payment_method'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('marketplace.offer_status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('marketplace.ordered_at'))
                    ->dateTime('j. n. Y H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('marketplace.offer_status'))
                    ->options(MarketOrderStatus::enumArray()),
            ])
            ->recordActions([
                $this->cancelOrderAction(),
            ])
            ->emptyStateHeading(__('marketplace.empty_my_orders'))
            ->emptyStateDescription(__('marketplace.empty_my_orders_description'))
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateActions([
                Action::make('browseMarketplace')
                    ->label(__('marketplace.browse_marketplace'))
                    ->icon('heroicon-o-shopping-bag')
                    ->url(MarketplaceList::getUrl()),
            ])
            ->paginated([10, 25, 50]);
    }

    private function cancelOrderAction(): Action
    {
        return Action::make('cancelOrder')
            ->label(__('marketplace.cancel_order'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->button()
            ->requiresConfirmation()
            ->modalHeading(__('marketplace.cancel_order'))
            ->modalDescription(__('marketplace.cancel_order_confirmation'))
            ->visible(fn (MarketOrder $record): bool => $record->status === MarketOrderStatus::Ordered
                && ($record->marketProduct?->marketOffer?->isOpenForOrders() ?? false))
            ->action(function (MarketOrder $record): void {
                try {
                    app(MarketplaceService::class)->cancelOrder($record);
                } catch (RuntimeException $exception) {
                    Notification::make()
                        ->title($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('marketplace.order_cancelled_notification'))
                    ->success()
                    ->send();
            });
    }
}

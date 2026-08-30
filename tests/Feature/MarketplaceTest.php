<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Enums\MarketOfferStatus;
use App\Enums\MarketOrderStatus;
use App\Enums\UserCreditType;
use App\Filament\Clusters\Other\Pages\MyMarketOfferList;
use App\Filament\Clusters\Other\Pages\MyMarketOrderList;
use App\Filament\Pages\MarketplaceList;
use App\Mail\MarketOfferAnnouncement;
use App\Mail\MarketOfferClosed;
use App\Models\AppSetting;
use App\Models\MarketOffer;
use App\Models\MarketOrder;
use App\Models\MarketProduct;
use App\Models\User;
use App\Models\UserCredit;
use App\Services\MarketplaceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function (): void {
    Cache::flush();
    AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, true);
});

it('denies marketplace pages when module is disabled', function (): void {
    AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, false);
    Cache::flush();

    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get(MarketplaceList::getUrl())->assertForbidden();
    $this->get(MyMarketOfferList::getUrl())->assertForbidden();
    $this->get(MyMarketOrderList::getUrl())->assertForbidden();
});

it('renders marketplace pages for member when module is enabled', function (): void {
    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get(MarketplaceList::getUrl())->assertOk();
    $this->get(MyMarketOfferList::getUrl())->assertOk();
    $this->get(MyMarketOrderList::getUrl())->assertOk();
});

it('orders a product and allocates quantity', function (): void {
    $product = MarketProduct::factory()->limited(10)->create(['unit_price' => 250.0]);
    $buyer = User::factory()->create(['active' => true]);

    $order = app(MarketplaceService::class)->order($product, $buyer, 3, 'poznámka');

    expect($order->status)->toBe(MarketOrderStatus::Ordered)
        ->and($order->unit_price)->toBe(250.0)
        ->and($order->totalAmount())->toBe(750.0)
        ->and($product->qtyRemaining())->toBe(7);
});

it('prevents ordering more than remaining quantity', function (): void {
    $product = MarketProduct::factory()->limited(2)->create();
    $buyer = User::factory()->create(['active' => true]);

    app(MarketplaceService::class)->order($product, $buyer, 2);

    expect(fn () => app(MarketplaceService::class)->order($product, $buyer, 1))
        ->toThrow(RuntimeException::class)
        ->and($product->qtyRemaining())->toBe(0);
});

it('prevents ordering from closed offer', function (): void {
    $offer = MarketOffer::factory()->closed()->create();
    $product = MarketProduct::factory()->create(['market_offer_id' => $offer->id]);
    $buyer = User::factory()->create(['active' => true]);

    expect(fn () => app(MarketplaceService::class)->order($product, $buyer, 1))
        ->toThrow(RuntimeException::class);
});

it('cancels order and releases quantity while offer is open', function (): void {
    $product = MarketProduct::factory()->limited(5)->create();
    $order = MarketOrder::factory()->create(['market_product_id' => $product->id, 'qty' => 5]);

    expect($product->isSoldOut())->toBeTrue();

    app(MarketplaceService::class)->cancelOrder($order);

    expect($order->refresh()->status)->toBe(MarketOrderStatus::Cancelled)
        ->and($product->qtyRemaining())->toBe(5);
});

it('prevents cancelling order of closed offer', function (): void {
    $offer = MarketOffer::factory()->closed()->create();
    $product = MarketProduct::factory()->create(['market_offer_id' => $offer->id]);
    $order = MarketOrder::factory()->create(['market_product_id' => $product->id]);

    expect(fn () => app(MarketplaceService::class)->cancelOrder($order))
        ->toThrow(RuntimeException::class);
});

it('closes an active offer', function (): void {
    $offer = MarketOffer::factory()->create();

    app(MarketplaceService::class)->closeOffer($offer);

    expect($offer->refresh()->status)->toBe(MarketOfferStatus::Closed)
        ->and($offer->closed_at)->not->toBeNull()
        ->and($offer->isOpenForOrders())->toBeFalse();

    expect(fn () => app(MarketplaceService::class)->closeOffer($offer))
        ->toThrow(RuntimeException::class);
});

it('creates order with price snapshot through marketplace table action', function (): void {
    $product = MarketProduct::factory()->limited(4)->create(['unit_price' => 100.0]);
    $buyer = User::factory()->create(['active' => true]);
    $this->actingAs($buyer);

    Livewire::test(MarketplaceList::class)
        ->callTableAction('order', $product, ['qty' => 2, 'note' => 'test'])
        ->assertHasNoTableActionErrors();

    $order = MarketOrder::query()->where('user_id', $buyer->id)->firstOrFail();

    expect($order->qty)->toBe(2)
        ->and($order->unit_price)->toBe(100.0)
        ->and($order->market_product_id)->toBe($product->id);
});

it('notifies author and orderers when offer is closed', function (): void {
    Mail::fake();

    $offer = MarketOffer::factory()->create();
    $product = MarketProduct::factory()->create(['market_offer_id' => $offer->id]);
    $buyer = User::factory()->create(['active' => true]);
    MarketOrder::factory()->create(['market_product_id' => $product->id, 'user_id' => $buyer->id]);

    app(MarketplaceService::class)->closeOffer($offer);

    Mail::assertQueued(MarketOfferClosed::class, 2);
    Mail::assertQueued(MarketOfferClosed::class, fn (MarketOfferClosed $mail): bool => $mail->hasTo($buyer->email));
});

it('bills closed offer with credit charge orders only', function (): void {
    Mail::fake();

    $author = User::factory()->create(['active' => true]);
    $offer = MarketOffer::factory()->create(['user_id' => $author->id]);

    $creditProduct = MarketProduct::factory()->create(['market_offer_id' => $offer->id, 'unit_price' => 200.0]);
    $directProduct = MarketProduct::factory()->directPayment()->create(['market_offer_id' => $offer->id]);
    $freeProduct = MarketProduct::factory()->free()->create(['market_offer_id' => $offer->id]);

    $buyer = User::factory()->create(['active' => true]);
    $creditOrder = MarketOrder::factory()->create([
        'market_product_id' => $creditProduct->id,
        'user_id' => $buyer->id,
        'qty' => 2,
        'unit_price' => 200.0,
    ]);
    $directOrder = MarketOrder::factory()->create(['market_product_id' => $directProduct->id, 'user_id' => $buyer->id]);
    $freeOrder = MarketOrder::factory()->create([
        'market_product_id' => $freeProduct->id,
        'user_id' => $buyer->id,
        'unit_price' => 0,
    ]);
    $cancelledOrder = MarketOrder::factory()->cancelled()->create(['market_product_id' => $creditProduct->id]);

    app(MarketplaceService::class)->closeOffer($offer);
    $billedCount = app(MarketplaceService::class)->billOffer($offer->refresh(), $author);

    expect($billedCount)->toBe(3)
        ->and($offer->refresh()->status)->toBe(MarketOfferStatus::Billed)
        ->and($creditOrder->refresh()->status)->toBe(MarketOrderStatus::Billed)
        ->and($directOrder->refresh()->status)->toBe(MarketOrderStatus::Billed)
        ->and($freeOrder->refresh()->status)->toBe(MarketOrderStatus::Billed)
        ->and($cancelledOrder->refresh()->status)->toBe(MarketOrderStatus::Cancelled);

    $credits = UserCredit::query()->where('credit_type', UserCreditType::MarketplaceBilling)->get();

    expect($credits)->toHaveCount(1)
        ->and($credits->first()->amount)->toBe(-400.0)
        ->and($credits->first()->user_id)->toBe($buyer->id)
        ->and($credits->first()->market_order_id)->toBe($creditOrder->id);
});

it('prevents billing an offer that is not closed', function (): void {
    $author = User::factory()->create(['active' => true]);
    $offer = MarketOffer::factory()->create(['user_id' => $author->id]);

    expect(fn () => app(MarketplaceService::class)->billOffer($offer, $author))
        ->toThrow(RuntimeException::class);
});

it('announces offer to active members except the author', function (): void {
    Mail::fake();

    $author = User::factory()->create(['active' => true]);
    $offer = MarketOffer::factory()->create(['user_id' => $author->id]);
    $members = User::factory()->count(2)->create(['active' => true]);
    User::factory()->create(['active' => false]);

    $count = app(MarketplaceService::class)->announceOffer($offer);

    expect($count)->toBeGreaterThanOrEqual(2);
    Mail::assertQueued(MarketOfferAnnouncement::class, fn (MarketOfferAnnouncement $mail): bool => $mail->hasTo($members->first()->email));
    Mail::assertNotQueued(MarketOfferAnnouncement::class, fn (MarketOfferAnnouncement $mail): bool => $mail->hasTo($author->email));
});

it('closes expired offers via command and notifies participants', function (): void {
    Mail::fake();

    $expired = MarketOffer::factory()->create(['closes_at' => now()->subHour()]);
    $running = MarketOffer::factory()->create(['closes_at' => now()->addDay()]);

    $this->artisan('marketplace:close-expired')->assertSuccessful();

    expect($expired->refresh()->status)->toBe(MarketOfferStatus::Closed)
        ->and($expired->closed_at)->not->toBeNull()
        ->and($running->refresh()->status)->toBe(MarketOfferStatus::Active);

    Mail::assertQueued(MarketOfferClosed::class);
});

it('allows billing club offer only to billing specialist through table action', function (): void {
    Mail::fake();

    $author = User::factory()->create(['active' => true]);
    $clubOffer = MarketOffer::factory()->clubOffer()->closed()->create(['user_id' => $author->id]);

    // Autor oddílové nabídky (bez role) akci nevidí.
    $this->actingAs($author);
    Livewire::test(MyMarketOfferList::class)
        ->assertTableActionHidden('billOffer', $clubOffer);

    // Pokladník akci vidí a rozúčtuje.
    Spatie\Permission\Models\Role::firstOrCreate(['name' => AppRoles::BillingSpecialist->value, 'guard_name' => 'web']);
    $billingSpecialist = User::factory()->create(['active' => true]);
    $billingSpecialist->assignRole(AppRoles::BillingSpecialist->value);
    $this->actingAs($billingSpecialist);

    Livewire::test(MyMarketOfferList::class)
        ->callTableAction('billOffer', $clubOffer)
        ->assertHasNoTableActionErrors();

    expect($clubOffer->refresh()->status)->toBe(MarketOfferStatus::Billed);
});

it('cancels own order through my orders table action', function (): void {
    $buyer = User::factory()->create(['active' => true]);
    $order = MarketOrder::factory()->create(['user_id' => $buyer->id]);
    $this->actingAs($buyer);

    Livewire::test(MyMarketOrderList::class)
        ->callTableAction('cancelOrder', $order)
        ->assertHasNoTableActionErrors();

    expect($order->refresh()->status)->toBe(MarketOrderStatus::Cancelled);
});

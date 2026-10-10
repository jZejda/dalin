<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Mail\EntryEndsToPay;
use App\Mail\EventEntryEnds;
use App\Mail\EventWeeklyEndsBySport;
use App\Mail\MarketOfferAnnouncement;
use App\Mail\MarketOfferClosed;
use App\Mail\NewPost;
use App\Mail\NewPosts;
use App\Mail\PreRaceSummaryMail;
use App\Mail\TransportOfferCancelled;
use App\Mail\TransportRequestCancelled;
use App\Mail\TransportRequestCreated;
use App\Mail\TransportRequestDecided;
use App\Mail\UserAppNotification;
use App\Mail\UserCreditChange;
use App\Mail\UserEntryNotification;
use App\Mail\UserPasswordSend;
use App\Mail\UsersInDebit;
use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\AppSetting;
use App\Models\BankTransaction;
use App\Models\MarketOffer;
use App\Models\MarketOrder;
use App\Models\MarketProduct;
use App\Models\Post;
use App\Models\UserCredit;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Models\Vehicle;
use App\Services\Mail\MailBranding;
use App\Services\Mail\MailMoney;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    // The testing DB may hold seeded club settings (e.g. the demo accent); start from none
    AppSetting::query()->delete();
    Cache::flush();
    App::setLocale('cs');
    config()->set('site-config.club.full_name', 'Orientační klub Testov');
    config()->set('site-config.club.abbr', 'TST');
    config()->set('site-config.club.technical_email', 'technik@testov.cz');
});

function clubMailWeeklyEvent(array $attributes = []): SportEvent
{
    return SportEvent::factory()->create([
        'name' => 'Oblastní přebor Testov',
        'oris_id' => null,
        'place' => 'Testov',
        'date' => now()->addDays(20),
        'entry_date_1' => now()->addDays(3)->setTime(23, 59),
        ...$attributes,
    ]);
}

it('builds the branding from club settings', function (): void {
    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#1F7A5C');
    AppSetting::set(AppSetting::SEO_SAME_AS, ['https://www.facebook.com/testov']);

    $brand = MailBranding::fromSettings();

    expect($brand->clubName)->toBe('Orientační klub Testov')
        ->and($brand->clubInitials)->toBe('TST')
        ->and($brand->accent)->toBe('#1f7a5c')
        ->and($brand->onAccent)->toBe('#ffffff')
        ->and($brand->logoUrl)->toBeNull()
        ->and($brand->contactEmail)->toBe('technik@testov.cz')
        ->and($brand->socialLinks)->toBe([['label' => 'Facebook', 'url' => 'https://www.facebook.com/testov']]);
});

it('falls back to the default accent when none or an invalid one is set', function (): void {
    expect(MailBranding::fromSettings()->accent)->toBe(MailBranding::DEFAULT_ACCENT);

    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, 'red');

    expect(MailBranding::fromSettings()->accent)->toBe(MailBranding::DEFAULT_ACCENT);
});

it('renders the weekly summary in the club layout with the club accent', function (): void {
    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#1f7a5c');
    $event = clubMailWeeklyEvent();

    $mail = new EventWeeklyEndsBySport(collect([$event]), collect(), collect());

    $mail->assertSeeInHtml('Orientační klub Testov')
        ->assertSeeInHtml('#1f7a5c', false)
        ->assertSeeInHtml('Vyber si svůj další závod.')
        ->assertSeeInHtml('První termín přihlášek')
        ->assertSeeInHtml('Nastavení oznámení')
        ->assertDontSeeInHtml('Druhý termín přihlášek')
        ->assertSeeInText('Oblastní přebor Testov')
        ->assertSeeInText('technik@testov.cz');
});

it('lists an event without ORIS id by its name in the weekly summary', function (): void {
    $event = clubMailWeeklyEvent(['name' => 'Klubový trénink bez ORISu', 'alt_name' => null]);

    (new EventWeeklyEndsBySport(collect([$event]), collect(), collect()))
        ->assertSeeInHtml('Klubový trénink bez ORISu')
        ->assertSeeInHtml($event->entry_date_1->format('j. n. · H:i'));
});

it('renders the pre-race summary with runners and course details', function (): void {
    $event = clubMailWeeklyEvent(['start_time' => '10:30:00', 'oris_id' => 9876]);
    $definition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => $event->sport_id, 'name' => 'D35'],
        ['age_from' => 35, 'age_to' => 39, 'gender' => 'F'],
    );
    SportClass::factory()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'distance' => '4.8',
        'controls' => '12',
        'climbing' => '160',
    ]);
    $profile = UserRaceProfile::query()->create([
        'user_id' => User::factory()->create()->id,
        'first_name' => 'Jana',
        'last_name' => 'Nováková',
        'reg_number' => 'TST8501',
        'gender' => 'F',
        'active' => true,
    ]);
    $entry = UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'user_race_profile_id' => $profile->id,
        'class_name' => 'D35',
        'requested_start' => '10:42',
        'entry_status' => EntryStatus::Create->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);

    (new PreRaceSummaryMail($event, collect([$entry])))
        ->assertSeeInHtml('Vše podstatné před startem.')
        ->assertSeeInHtml('Jana Nováková')
        ->assertSeeInHtml('Start 10:42 · +12 min')
        ->assertSeeInHtml('4.8 km · 12 kontrol · ↑ 160 m')
        ->assertSeeInHtml('Zavod?id=9876', false)
        ->assertSeeInText('TST8501');
});

function clubMailTransportRequest(array $attributes = []): TransportRequest
{
    $event = clubMailWeeklyEvent(['date' => '2026-11-14']);
    $driver = User::factory()->create(['name' => 'Petr Dvořák']);
    $offer = TransportOffer::factory()->create([
        'sport_event_id' => $event->id,
        'user_id' => $driver->id,
        'vehicle_id' => Vehicle::factory()->ownedBy($driver)->create(['name' => 'Rodinné kombi'])->id,
        'direction' => TransportDirection::Both,
        'departure_place' => 'Brno, Riviéra',
    ]);

    return TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => User::factory()->create(['name' => 'Romana Klímová'])->id,
        'direction' => TransportDirection::Both,
        'seats' => 2,
        'note' => 'Mám s sebou kolo na střeše.',
        ...$attributes,
    ]);
}

it('renders the car-sharing request with approve button and reject link', function (): void {
    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#ffd329');
    $request = clubMailTransportRequest();

    (new TransportRequestCreated($request, 'https://example.test/approve', 'https://example.test/reject'))
        ->assertSeeInHtml('Romana Klímová chce jet s tebou.')
        ->assertSeeInHtml('2 místa')
        ->assertSeeInHtml('Rodinné kombi')
        ->assertSeeInHtml('Poznámka k žádosti')
        ->assertSeeInHtml('„Mám s sebou kolo na střeše.“')
        ->assertSeeInHtml('Odjezd z')
        ->assertSeeInHtml('https://example.test/approve', false)
        ->assertSeeInHtml('club-secondary-negative', false)
        ->assertSeeInText('https://example.test/reject');
});

it('renders the approved car-sharing request with the driver and a link to the transport tab', function (): void {
    $request = clubMailTransportRequest(['status' => TransportRequestStatus::Approved]);
    $event = $request->transportOffer?->sportEvent;

    (new TransportRequestDecided($request))
        ->assertSeeInHtml('Máš potvrzené místo v autě.')
        ->assertSeeInHtml('Tvoje žádost o spolujízdu na Oblastní přebor Testov, 14. listopadu 2026, byla schválena.')
        ->assertSeeInHtml('Schváleno · 2 místa')
        ->assertSeeInHtml('club-status-positive', false)
        ->assertSeeInHtml('Rezervovaná místa')
        ->assertSeeInHtml('Petr Dvořák')
        ->assertSeeInHtml('Brno, Riviéra')
        ->assertSeeInHtml('Otevřít dopravu k závodu')
        ->assertSeeInText(SportEventResource::getUrl('entry', ['record' => $event], panel: 'admin'))
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('renders the rejected car-sharing request with a negative status', function (): void {
    $request = clubMailTransportRequest(['status' => TransportRequestStatus::Rejected]);

    (new TransportRequestDecided($request))
        ->assertSeeInHtml('Tentokrát spolujízda nevyšla.')
        ->assertSeeInHtml('Žádost zamítnuta')
        ->assertSeeInHtml('club-status-negative', false)
        ->assertSeeInHtml('Prohlédnout další nabídky dopravy')
        ->assertDontSeeInHtml('Rezervovaná místa')
        ->assertDontSeeInHtml('Máš potvrzené místo v autě.');
});

it('tells the driver which passenger cancelled and how many seats freed up', function (): void {
    $request = clubMailTransportRequest();

    (new TransportRequestCancelled($request))
        ->assertSeeInHtml('Romana Klímová ruší rezervaci.')
        ->assertSeeInHtml('Na Oblastní přebor Testov, 14. listopadu 2026, se ve tvém autě uvolnila 2 rezervovaná místa.')
        ->assertSeeInHtml('Zrušená místa')
        ->assertSeeInHtml('Brno, Riviéra')
        ->assertSeeInHtml('Otevřít svoji nabídku dopravy');
});

it('still names the race and driver when the cancelled offer was deleted', function (): void {
    $request = clubMailTransportRequest();
    $request->transportOffer?->delete();

    (new TransportOfferCancelled(TransportRequest::query()->findOrFail($request->id)))
        ->assertSeeInHtml('Řidič zrušil nabídku dopravy.')
        ->assertSeeInHtml('Spolujízda na Oblastní přebor Testov, 14. listopadu 2026, se ruší.')
        ->assertSeeInHtml('Petr Dvořák')
        ->assertSeeInHtml('Počítej s jinou dopravou')
        ->assertSeeInHtml('Prohlédnout dopravu k závodu');
});

it('renders the car-sharing mails in English', function (): void {
    App::setLocale('en');
    $request = clubMailTransportRequest(['status' => TransportRequestStatus::Approved]);

    (new TransportRequestDecided($request))
        ->assertSeeInHtml('Your seat in the car is confirmed.')
        ->assertSeeInHtml('Approved · 2 seats')
        ->assertSeeInHtml('November 14, 2026');

    (new TransportRequestCancelled($request))
        ->assertSeeInHtml('2 reserved seats in your car to Oblastní přebor Testov, November 14, 2026, are free again.');

    (new TransportRequestCreated($request, 'https://example.test/approve', 'https://example.test/reject'))
        ->assertSeeInHtml('“Mám s sebou kolo na střeše.”');
});

it('reminds the member of the approaching first entry deadline', function (): void {
    $event = clubMailWeeklyEvent(['oris_id' => 18421, 'date' => '2026-10-24', 'entry_date_1' => '2026-10-14 23:59:00']);

    (new EventEntryEnds(collect([$event]), 3))
        ->assertSeeInHtml('Ještě stihneš první termín.')
        ->assertSeeInHtml('Za 3 dny končí první termín přihlášek na následující závody.')
        ->assertSeeInHtml('ORIS 18421')
        ->assertSeeInHtml('Přihlášky do 14. 10. · 23:59')
        ->assertSeeInHtml('Vybrat závod a přihlásit se')
        ->assertSeeInHtml('Nastavení oznámení')
        ->assertSeeInText('24. ŘÍJ – Oblastní přebor Testov');
});

it('asks billing specialists to pay the entry fees for the given deadline', function (): void {
    $event = clubMailWeeklyEvent([
        'oris_id' => 18422,
        'date' => '2026-10-25',
        'entry_date_1' => '2026-10-08 23:59:00',
        'entry_date_2' => '2026-10-15 23:59:00',
    ]);

    $mail = new EntryEndsToPay(collect([$event]), 2);

    $mail->assertHasSubject(config('app.name').' - Startovné k úhradě – druhý termín přihlášek');
    $mail->assertSeeInHtml('Je čas uhradit startovné.')
        ->assertSeeInHtml('Končí druhý termín přihlášek.')
        ->assertSeeInHtml('Druhý termín přihlášek')
        ->assertSeeInHtml('Přihlášky do 15. 10. · 23:59')
        ->assertSeeInHtml('ORIS 18422')
        ->assertSeeInHtml('Platební údaje a konkrétní částku ověř v podkladech pořadatele.')
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('renders the deadline mails in English', function (): void {
    App::setLocale('en');
    $event = clubMailWeeklyEvent(['entry_date_1' => '2026-10-14 23:59:00']);

    (new EventEntryEnds(collect([$event]), 1))
        ->assertSeeInHtml('The first entry deadline for the following races ends in 1 day.')
        ->assertSeeInHtml('Entries until 14. 10. · 23:59');

    (new EntryEndsToPay(collect([$event]), 1))
        ->assertHasSubject(config('app.name').' - Entry fees due – first entry deadline')
        ->assertSeeInHtml('First entry deadline');
});

function clubMailCredit(User $user, float $amount, ?int $bankTransactionId = null): UserCredit
{
    $credit = new UserCredit();
    $credit->user_id = $user->id;
    $credit->amount = $amount;
    $credit->currency = 'CZK';
    $credit->bank_transaction_id = $bankTransactionId;
    $credit->source = UserCreditSource::User->value;
    $credit->status = UserCreditStatus::Done;
    $credit->credit_type = UserCreditType::UserDonation;
    $credit->saveOrFail();

    return $credit;
}

it('formats money amounts for the club mails', function (): void {
    expect(MailMoney::format(2000.0))->toBe("2\u{00A0}000\u{00A0}Kč")
        ->and(MailMoney::format(-320.0))->toBe("\u{2212}320\u{00A0}Kč")
        ->and(MailMoney::format(2000.0, signed: true))->toBe("+2\u{00A0}000\u{00A0}Kč")
        ->and(MailMoney::format(12.5))->toBe("12,50\u{00A0}Kč");

    App::setLocale('en');

    expect(MailMoney::format(2000.0))->toBe("2,000\u{00A0}CZK")
        ->and(MailMoney::format(-12.5, 'EUR'))->toBe("\u{2212}12.50\u{00A0}EUR");
});

it('sends login details for a new account', function (): void {
    $user = User::factory()->create(['name' => 'Jana Nováková', 'email' => 'jana@example.test']);

    (new UserPasswordSend('Ukazka-7xP4', $user))
        ->assertSeeInHtml('Vítej ve svém klubu.')
        ->assertSeeInHtml('Jana Nováková')
        ->assertSeeInHtml('jana@example.test')
        ->assertSeeInHtml('Ukazka-7xP4')
        ->assertSeeInHtml('/admin/login')
        ->assertSeeInHtml('Přihlásit se do DaLinu')
        ->assertSeeInHtml('Jak začít')
        ->assertSeeInHtml('napoveda/ovladani-aplikace', false)
        ->assertSeeInHtml('S přihlášením ti pomůže správce klubu: technik@testov.cz.')
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('sends login details after a password reset', function (): void {
    $user = User::factory()->create();

    (new UserPasswordSend('Ukazka-7xP4', $user, UserPasswordSend::ACTION_RESET_PASSWORD))
        ->assertSeeInHtml('Tvoje heslo bylo resetováno.')
        ->assertSeeInHtml('Nápověda k přihlášení')
        ->assertSeeInHtml('Po přihlášení si můžeš nastavit vlastní heslo.')
        ->assertDontSeeInHtml('Vítej ve svém klubu.');
});

it('shows the balance and the bank reference of a credited payment', function (): void {
    $user = User::factory()->create();
    clubMailCredit($user, 450.0);
    $bankTransaction = BankTransaction::factory()->create();
    $credit = clubMailCredit($user, 2000.0, $bankTransaction->id);

    (new UserCreditChange($user->refresh(), $credit))
        ->assertSeeInHtml("Na účtu přibylo 2\u{00A0}000\u{00A0}Kč.")
        ->assertSeeInHtml('Aktuální zůstatek')
        ->assertSeeInHtml("2\u{00A0}450\u{00A0}Kč")
        ->assertSeeInHtml("+2\u{00A0}000\u{00A0}Kč")
        ->assertSeeInHtml('ID bankovní transakce')
        ->assertSeeInHtml('S dotazy k pohybu na účtu kontaktuj klub: technik@testov.cz.')
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('shows a manual debit without an empty bank reference', function (): void {
    $user = User::factory()->create();
    $credit = clubMailCredit($user, -320.0);

    (new UserCreditChange($user->refresh(), $credit))
        ->assertSeeInHtml("Z účtu odešlo 320\u{00A0}Kč.")
        ->assertSeeInHtml("\u{2212}320\u{00A0}Kč")
        ->assertSeeInHtml('ID transakce')
        ->assertDontSeeInHtml('ID bankovní transakce');
});

it('renders the account mails in English', function (): void {
    App::setLocale('en');
    $user = User::factory()->create();
    $credit = clubMailCredit($user, 500.0);

    (new UserPasswordSend('Ukazka-7xP4', $user))
        ->assertSeeInHtml('Welcome to your club.')
        ->assertSeeInHtml('Getting started');

    (new UserCreditChange($user->refresh(), $credit))
        ->assertSeeInHtml("500\u{00A0}CZK was added to your account.")
        ->assertSeeInHtml('Current balance');
});

it('lists members with a negative balance for billing specialists', function (): void {
    $debtor = User::factory()->create(['name' => 'Tomáš Dlužník', 'email' => 'tomas.dluznik@example.test']);
    clubMailCredit($debtor, 200.0);
    clubMailCredit($debtor, -1480.0);
    $solvent = User::factory()->create(['name' => 'Eva Solventní']);
    clubMailCredit($solvent, 300.0);

    (new UsersInDebit())
        ->assertSeeInHtml('Přehled záporných zůstatků.')
        ->assertSeeInHtml('Členové s nízkým kreditem')
        ->assertSeeInHtml('Tomáš Dlužník')
        ->assertSeeInHtml('tomas.dluznik@example.test')
        ->assertSeeInHtml("\u{2212}1\u{00A0}280\u{00A0}Kč")
        ->assertSeeInHtml('club-person-amount', false)
        ->assertDontSeeInHtml('Eva Solventní')
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('renders the organiser message to race entrants as Markdown with the race details', function (): void {
    $event = clubMailWeeklyEvent(['alt_name' => 'podzimní oblastní žebříček', 'place' => 'Brno, Mariánské údolí', 'date' => '2026-10-24']);

    (new UserEntryNotification($event, 'Změna shromaždiště', "Shromaždiště se **přesouvá** na louku.\n\n<script>alert(1)</script>", null))
        ->assertSeeInHtml('Nové informace k závodu.')
        ->assertSeeInHtml('Oblastní přebor Testov · podzimní oblastní žebříček')
        ->assertSeeInHtml('24. října 2026')
        ->assertSeeInHtml('Brno, Mariánské údolí')
        ->assertSeeInHtml('Zpráva od klubu')
        ->assertSeeInHtml('přesouvá</strong>', false)
        ->assertDontSeeInHtml('<script>', false);
});

it('shows the sender, not the recipient, of a message from the club', function (): void {
    $sender = User::factory()->create(['name' => 'Petr Svoboda']);
    $this->travelTo(now()->setDate(2026, 10, 10)->setTime(15, 30));

    $mail = new UserAppNotification($sender, 'Klubové oblečení', 'Oblečení si můžeš *vyzvednout* na tréninku.', 'petr@example.test');
    $this->travelTo(now()->addHours(2));

    $mail->assertSeeInHtml('Máš zprávu od klubu.')
        ->assertSeeInHtml('Odesílatel')
        ->assertSeeInHtml('Petr Svoboda')
        ->assertSeeInHtml('10. 10. 2026 · 15:30')
        ->assertSeeInHtml('vyzvednout</em>', false);
    expect($mail->hasReplyTo('petr@example.test'))->toBeTrue();
});

it('renders the report and messages in English', function (): void {
    App::setLocale('en');
    $event = clubMailWeeklyEvent();

    (new UsersInDebit())->assertSeeInHtml('Negative balances overview.');
    (new UserEntryNotification($event, 'Info', 'Hello', null))->assertSeeInHtml('Message from the club');
    (new UserAppNotification(null, 'Info', 'Hello', null))
        ->assertSeeInHtml('You have a message from the club.')
        ->assertDontSeeInHtml('Sender');
});

function clubMailPost(string $title, string $content, ContentFormat $format = ContentFormat::Html): Post
{
    return Post::query()->create([
        'user_id' => User::factory()->create()->id,
        'title' => $title,
        'content' => $content,
        'content_mode' => $format,
        'private' => PostStatus::Public,
    ]);
}

it('renders the article HTML instead of raw tags and strips unsafe markup', function (): void {
    $post = clubMailPost(
        'Podzimní soustředění v terénu',
        "<p>Zaměříme se na <strong>čtení vrstevnic</strong>.</p>\n\n<ul><li>Sobota: technický trénink</li></ul>"
            .'<script>alert(1)</script><p onclick="steal()">Podrobnosti brzy.</p><img src="/storage/posts/mapa.jpg" alt="Mapa">',
    );

    $mail = new NewPost($post, 'Soustředění');

    $mail->assertSeeInHtml('Co je nového v klubu.')
        ->assertSeeInHtml('Podzimní soustředění v terénu')
        ->assertSeeInHtml('čtení vrstevnic</strong>', false)
        ->assertSeeInHtml('Sobota: technický trénink</li>', false)
        ->assertSeeInHtml('Podrobnosti brzy.</p>', false)
        ->assertSeeInHtml('src="'.url('/storage/posts/mapa.jpg').'"', false)
        ->assertDontSeeInHtml('&lt;p&gt;', false)
        ->assertDontSeeInHtml('<script', false)
        ->assertDontSeeInHtml('onclick', false)
        ->assertDontSeeInHtml('<pre', false)
        ->assertSeeInHtml('Nastavení oznámení')
        ->assertSeeInText('- Sobota: technický trénink')
        ->assertDontSeeInText('<p>');
    $mail->assertHasSubject(config('app.name').' - '.__('mail/new-post.subject.new_post').' - Soustředění');
});

it('renders Markdown articles and escapes HTML written into them', function (): void {
    $post = clubMailPost('Trénink', "Sraz je **v 17:00**.\n\n<b onclick=\"x()\">tučné</b>", ContentFormat::Markdown);

    (new NewPost($post, null))
        ->assertSeeInHtml('v 17:00</strong>', false)
        ->assertSeeInHtml('&lt;b onclick', false)
        ->assertDontSeeInHtml('<b onclick', false);
});

it('lists every article of the news digest with a separator', function (): void {
    $first = clubMailPost('Podzimní soustředění v terénu', '<p>Víkend mapových tréninků.</p>');
    $second = clubMailPost('Klubový trénink v úterý', '<p>Sraz v 17:00 u lesního parkoviště.</p>');

    (new NewPosts(collect([$first, $second])))
        ->assertSeeInHtml('2 zprávy, které se hodí vědět.')
        ->assertSeeInHtml('Podzimní soustředění v terénu')
        ->assertSeeInHtml('Klubový trénink v úterý')
        ->assertSeeInHtml('club-article-separated', false)
        ->assertSeeInHtml('Nastavení oznámení')
        ->assertSeeInText('Sraz v 17:00 u lesního parkoviště.');
});

function clubMailMarketOffer(): MarketOffer
{
    $offer = MarketOffer::factory()->create([
        'user_id' => User::factory()->create(['name' => 'Petr Svoboda'])->id,
        'is_club_offer' => true,
        'title' => 'Podzimní oblečení',
        'description' => 'Společná objednávka klubového oblečení.',
        'closes_at' => '2026-10-18 20:00:00',
    ]);
    MarketProduct::factory()->limited(20)->create(['market_offer_id' => $offer->id, 'name' => 'Klubový dres', 'unit_price' => 850.0]);
    MarketProduct::factory()->directPayment()->create(['market_offer_id' => $offer->id, 'name' => 'Čelenka', 'unit_price' => 180.0]);
    MarketProduct::factory()->free()->limited(30)->create(['market_offer_id' => $offer->id, 'name' => 'Starší mapy']);

    return $offer->refresh();
}

it('announces a marketplace offer with its products and order deadline', function (): void {
    (new MarketOfferAnnouncement(clubMailMarketOffer()))
        ->assertSeeInHtml('Na tržišti je nová klubová nabídka.')
        ->assertSeeInHtml('Petr Svoboda zveřejňuje za klub nabídku „Podzimní oblečení“.')
        ->assertSeeInHtml('Společná objednávka klubového oblečení.')
        ->assertSeeInHtml('Objednávky do 18. 10. 2026 · 20:00')
        ->assertSeeInHtml('Klubový dres')
        ->assertSeeInHtml('Cena za kus · k dispozici 20 ks')
        ->assertSeeInHtml("850\u{00A0}Kč")
        ->assertSeeInHtml('Cena za kus · množství bez omezení')
        ->assertSeeInHtml('K dispozici 30 ks')
        ->assertSeeInHtml('Zdarma')
        ->assertSeeInHtml('Prohlédnout nabídku')
        // Members can't opt out of marketplace announcements, so no settings link
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('tells a buyer what they ordered and how each item is paid', function (): void {
    $offer = clubMailMarketOffer();
    $buyer = User::factory()->create();
    [$jersey, $headband, $maps] = $offer->products->sortBy('id')->values()->all();
    MarketOrder::factory()->create(['market_product_id' => $jersey->id, 'user_id' => $buyer->id, 'qty' => 1, 'unit_price' => 850.0]);
    MarketOrder::factory()->create(['market_product_id' => $headband->id, 'user_id' => $buyer->id, 'qty' => 2, 'unit_price' => 180.0]);
    MarketOrder::factory()->create(['market_product_id' => $maps->id, 'user_id' => $buyer->id, 'qty' => 3, 'unit_price' => 0]);
    MarketOrder::factory()->cancelled()->create(['market_product_id' => $jersey->id, 'user_id' => $buyer->id, 'qty' => 5, 'unit_price' => 850.0]);
    $offer->update(['closed_at' => '2026-10-18 20:00:00']);

    (new MarketOfferClosed($offer->refresh(), $buyer))
        ->assertSeeInHtml('Tvoje objednávka je uzavřená.')
        ->assertSeeInHtml('Nabídka „Podzimní oblečení“ (Petr Svoboda) byla uzavřena 18. 10. 2026 · 20:00.')
        ->assertSeeInHtml('Klubový dres · 1 ks')
        ->assertSeeInHtml("850\u{00A0}Kč za kus · platba z klubového kreditu")
        ->assertSeeInHtml('Čelenka · 2 ks')
        ->assertSeeInHtml("360\u{00A0}Kč")
        ->assertSeeInHtml('platba přímo prodejci')
        ->assertSeeInHtml('Starší mapy · 3 ks')
        ->assertSeeInHtml('Bez úhrady')
        ->assertSeeInHtml('Jak proběhne úhrada')
        ->assertSeeInHtml('U položek s přímou platbou se domluv s prodejcem.')
        ->assertDontSeeInHtml('5 ks')
        ->assertDontSeeInHtml('Další postup');
});

it('gives the offer author the next steps instead of a buyer summary', function (): void {
    $offer = clubMailMarketOffer();

    (new MarketOfferClosed($offer, $offer->user))
        ->assertSeeInHtml('Tvoje nabídka je uzavřená.')
        ->assertSeeInHtml('Další postup')
        ->assertSeeInHtml('Moje nabídky')
        ->assertDontSeeInHtml('Tvoje objednávky')
        ->assertDontSeeInHtml('Jak proběhne úhrada');
});

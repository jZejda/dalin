<?php

declare(strict_types=1);

use App\Livewire\Shared\Maps\LeafletMap;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCreditNote;
use App\Shared\Helpers\Markdown;

use function Pest\Livewire\livewire;

it('keeps markdown formatting and shows inline html as text', function () {
    $html = (string) Markdown::userText("**tučně** <img src=x onerror=alert(1)>\n\n[odkaz](javascript:alert(1)) [web](https://example.com)");

    expect($html)
        ->toContain('<strong>tučně</strong>')
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->toContain('<a href="https://example.com">web</a>')
        ->not->toContain('<img')
        ->not->toContain('javascript:');
});

it('renders an empty note without errors', function () {
    expect((string) Markdown::userText(null))->toBe('');
});

it('renders credit notes as text in every notes view', function (string $view, string $variable) {
    $this->actingAs(User::factory()->create());

    $note = UserCreditNote::factory()->make(['note' => 'Platba <script>alert(1)</script> **ok**']);
    $note->created_at = now();
    $note->setRelation('userNoteMade', User::factory()->make(['name' => 'Jana Nováková']));

    $data = $variable === 'record'
        ? ['record' => (object) ['userCreditNotes' => collect([$note])]]
        : [$variable => [$note]];

    $html = view($view, $data)->render();

    expect($html)
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->toContain('<strong>ok</strong>')
        ->not->toContain('<script>alert(1)</script>');
})->with([
    'credit modal' => ['filament.modals.user-add-credit-admin', 'record'],
    'notes partial' => ['partials.backend.user-credit-notes', 'notes'],
]);

it('encodes event names in the map popup', function () {
    $event = SportEvent::factory()->create([
        'name' => "O'Brien Cup </script><script>alert(1)</script>",
        'alt_name' => '<b>Lesní běh</b>',
        'gps_lat' => '49.2',
        'gps_lon' => '16.6',
    ]);

    $html = livewire(LeafletMap::class, ['sportEvent' => $event])->html();

    // Js::from() emits a JS string literal with HTML-significant characters as unicode escapes
    expect($html)
        ->toContain('O\\u0026#039;Brien Cup \\u0026lt;')
        ->toContain('\\u0026lt;b\\u0026gt;Lesní běh')
        ->not->toContain('<script>alert(1)</script>')
        ->not->toContain("O'Brien")
        ->not->toContain('<b>Lesní běh</b>');
});

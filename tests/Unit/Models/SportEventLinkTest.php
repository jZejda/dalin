<?php

declare(strict_types=1);

use App\Enums\SportEventLinkType;
use App\Models\SportEventLink;

function makeLink(array $attributes): SportEventLink
{
    return new SportEventLink([...['source_type' => SportEventLinkType::Other], ...$attributes]);
}

it('labels a link by its name', function (): void {
    expect(makeLink(['name_cz' => 'Rozpis', 'description_cz' => 'Verze 2'])->label())->toBe('Rozpis');
});

it('labels an ORIS "other" link by its description instead of the generic type', function (): void {
    app()->setLocale('cs');

    expect(makeLink(['name_cz' => null, 'description_cz' => ' Fotky z terénu '])->label())->toBe('Fotky z terénu');
});

it('falls back to the link type when there is no name or description', function (): void {
    app()->setLocale('cs');

    expect(makeLink(['source_type' => SportEventLinkType::Invitation, 'description_cz' => ''])->label())
        ->toBe(__('sport-event.type_enum_links.invitation'));
});

it('prefers English texts in English and falls back to the Czech ones', function (): void {
    app()->setLocale('en');

    expect(makeLink(['name_cz' => 'Pokyny', 'name_en' => 'Information'])->label())->toBe('Information')
        ->and(makeLink(['description_cz' => 'Štafetová seznamka', 'description_en' => ''])->label())->toBe('Štafetová seznamka');
});

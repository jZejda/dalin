<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('shows the terrain design system without loading club data or filament assets', function () {
    $this->withoutVite();

    $this->get(route('design-system'))
        ->assertOk()
        ->assertSee('Terrain — DALIN design system')
        ->assertSee('Design tokeny')
        ->assertSee('Ilustrační data')
        ->assertSee('data-terrain-theme', false)
        ->assertSee('Podle systému')
        ->assertSee('aria-invalid="true"', false)
        ->assertDontSee('filament/filament', false);
});

it('renders navigation as a link and disabled navigation as a disabled button', function () {
    $link = Blade::render('<x-ui.button href="/akce/322" data-test="event">Akce</x-ui.button>');
    $disabled = Blade::render('<x-ui.button href="/akce/322" disabled>Akce</x-ui.button>');

    expect($link)->toContain('<a href="/akce/322"', 'data-test="event"');
    expect($disabled)->toContain('<button', 'disabled')->not->toContain('href=');
});

it('connects the input label and error message to the field and escapes its value', function () {
    $html = Blade::render('<x-ui.input id="email" label="E-mail" error="Neplatný e-mail" value="&lt;script&gt;" />');

    expect($html)->toContain('for="email"', 'id="email"', 'aria-describedby="email-description"', 'id="email-description"', 'aria-invalid="true"')
        ->not->toContain('<script>');
});

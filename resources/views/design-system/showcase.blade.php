<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Terrain — DALIN design system</title>
    <x-ui.theme-script />
    @vite('resources/css/terrain.css')
</head>
<body class="terrain antialiased">
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:z-10 focus:bg-terrain-accent focus:text-terrain-on-accent focus:p-4">Přejít na obsah</a>
    <header class="bg-terrain-nav text-terrain-on-nav">
        <div class="mx-auto flex max-w-terrain flex-wrap items-center justify-between gap-4 px-6 py-5">
            <a href="/" class="font-bold tracking-[0.2em] focus-visible:outline-terrain-accent!">DALIN</a>
            <nav aria-label="Sekce design systému" class="flex flex-wrap gap-x-6 gap-y-3 text-sm">
                <a href="#tokens" class="hover:underline focus-visible:outline-terrain-accent!">Tokeny</a>
                <a href="#components" class="hover:underline focus-visible:outline-terrain-accent!">Komponenty</a>
                <a href="#example" class="hover:underline focus-visible:outline-terrain-accent!">Ukázka akce</a>
            </nav>
            <div class="flex items-center gap-3 text-sm">
                <label for="terrain-theme">Vzhled</label>
                <select id="terrain-theme" data-terrain-theme class="min-h-11 rounded-terrain-control border border-terrain-line bg-terrain-nav px-3 text-terrain-on-nav focus-visible:outline-terrain-accent!">
                    <option value="system">Podle systému</option>
                    <option value="light">Světlý</option>
                    <option value="dark">Tmavý</option>
                </select>
            </div>
        </div>
    </header>
    <main id="content">
        <section class="terrain-contours border-b border-terrain-line">
            <div class="mx-auto max-w-terrain px-6 py-terrain-section">
                <p class="mb-5 text-xs font-semibold uppercase tracking-[0.25em] text-terrain-secondary">DALIN / Design system / 01</p>
                <h1 class="text-terrain-display font-extrabold">Terrain<span class="ml-5 inline-block h-1.5 w-16 bg-terrain-accent align-middle" aria-hidden="true"></span></h1>
                <p class="mt-6 max-w-lg text-xl leading-relaxed">Jasný směr. Více prostoru.<br>Orientace v klubu i v terénu.</p>
                <p class="mt-4 max-w-lg text-sm leading-relaxed text-terrain-secondary">Základ vizuálního jazyka pro frontend DALINu. Charcoal, žlutý akcent a jemné vrstevnice. Obsah má přednost před dekorací.</p>
                <div class="mt-8"><x-ui.button href="#components">Prohlédnout komponenty <span aria-hidden="true">↓</span></x-ui.button></div>
            </div>
        </section>
        <div class="mx-auto max-w-terrain px-6">
            <section id="tokens" class="border-b border-terrain-line py-terrain-section">
                <x-ui.section-heading>01 / Design tokeny</x-ui.section-heading>
                <p class="mb-8 max-w-2xl text-terrain-secondary">Žlutá zvýrazňuje hlavní akci. Text a povrchy se přizpůsobují světlému i tmavému režimu. Hodnoty níže uvádějí světlou / tmavou paletu. Stav vždy doprovází slovní označení.</p>
                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ([['Ink', '#202426 / #F3F4EF', 'bg-terrain-ink'], ['Accent', '#FFD329', 'bg-terrain-accent'], ['Paper', '#FAFAF8 / #171B1D', 'bg-terrain-paper'], ['Surface', '#FFFFFF / #202628', 'bg-terrain-surface'], ['Muted', '#F0F1EE / #2A3134', 'bg-terrain-muted'], ['Line', '#DCDED9 / #454E52', 'bg-terrain-line']] as [$label, $hex, $class])
                        <div><div class="{{ $class }} mb-3 h-24 rounded-terrain-control border border-terrain-line" aria-hidden="true"></div><p class="text-sm font-semibold">{{ $label }}</p><p class="mt-1 font-mono text-xs text-terrain-secondary">{{ $hex }}</p></div>
                    @endforeach
                </div>
                <div class="mt-12 grid gap-10 md:grid-cols-2">
                    <div>
                        <h3 class="mb-5 text-sm font-semibold uppercase tracking-widest text-terrain-secondary">Typografie</h3>
                        <p class="text-4xl font-extrabold tracking-tight sm:text-5xl">Orientace spojuje</p>
                        <p class="mt-4 text-2xl font-bold tracking-tight">Nadcházející akce</p>
                        <p class="mt-4 leading-relaxed">Přehledné informace pro závodníky, pořadatele i klub. Systémové písmo bez závislosti na externích službách.</p>
                        <p class="mt-3 text-sm text-terrain-secondary">Sekundární text · 14 px / datum, místo, metadata</p>
                    </div>
                    <div>
                        <h3 class="mb-5 text-sm font-semibold uppercase tracking-widest text-terrain-secondary">Prostor a povrchy</h3>
                        <div class="flex flex-wrap items-end gap-5">
                            @foreach ([1, 2, 3, 4, 6, 8] as $space)
                                <div><div class="bg-terrain-accent" style="width: {{ $space * 4 }}px; height: {{ $space * 4 }}px" aria-hidden="true"></div><p class="mt-2 font-mono text-xs">{{ $space * 4 }} px</p></div>
                            @endforeach
                        </div>
                        <p class="mt-6 text-sm leading-relaxed text-terrain-secondary">Rytmus 4 px · ovládací prvky 8 px radius · samostatné objekty 12 px radius · obsah nejvýše 72 rem. Linky místo stínů, sekce místo karet.</p>
                    </div>
                </div>
            </section>
            <section id="components" class="border-b border-terrain-line py-terrain-section">
                <x-ui.section-heading>02 / Základní komponenty</x-ui.section-heading>
                <h3 class="mb-4 font-semibold">Akce</h3>
                <div class="flex flex-wrap gap-3">
                    <x-ui.button href="#example">Hlavní akce <span aria-hidden="true">→</span></x-ui.button>
                    <x-ui.button variant="secondary" href="#example">Vedlejší akce</x-ui.button>
                    <x-ui.button variant="quiet" href="#example">Textová akce →</x-ui.button>
                    <x-ui.button disabled>Nedostupná akce</x-ui.button>
                </div>
                <p class="mt-4 text-sm text-terrain-secondary">Odkazy navigují, tlačítka provádějí akce. Minimální výška 44 px, viditelný focus při ovládání klávesnicí.</p>
                <h3 class="mb-4 mt-10 font-semibold">Štítky a stavy</h3>
                <div class="flex flex-wrap gap-3"><x-ui.badge>OB · klasická trať</x-ui.badge><x-ui.badge tone="success">Přihlášky otevřeny</x-ui.badge><x-ui.badge tone="warning">Blíží se uzávěrka</x-ui.badge><x-ui.badge tone="danger">Po uzávěrce</x-ui.badge></div>
                <h3 class="mb-4 mt-10 font-semibold">Formulářové prvky</h3>
                <div class="grid gap-6 md:grid-cols-3">
                    <x-ui.input id="showcase-name" label="Jméno" placeholder="Jana Nováková" hint="Jméno uvedené na přihlášce." autocomplete="name" />
                    <x-ui.input id="showcase-email" label="E-mail" type="email" value="jana@" error="Zadejte platnou e-mailovou adresu." autocomplete="email" />
                    <x-ui.input id="showcase-club" label="Klub" value="ABM Brno" disabled hint="Ukázka nedostupného pole." />
                </div>
            </section>
            <section id="example" class="py-terrain-section">
                <x-ui.section-heading>03 / Ukázka akce<x-slot:action><span class="text-sm text-terrain-secondary">Ilustrační data</span></x-slot:action></x-ui.section-heading>
                <div class="terrain-contours border-y border-terrain-line py-8 sm:py-12">
                    <p class="mb-3 text-sm text-terrain-secondary">Kalendář / Závody</p>
                    <h3 class="max-w-2xl text-3xl font-extrabold tracking-tight sm:text-4xl">7. Jihomoravská liga</h3>
                    <p class="mt-2 text-lg">Oblastní žebříček</p>
                    <dl class="mt-6 flex flex-wrap gap-x-8 gap-y-4 text-sm">
                        <div><dt class="text-terrain-secondary">Datum</dt><dd class="mt-1 font-semibold">23. 5. 2026</dd></div>
                        <div><dt class="text-terrain-secondary">Start</dt><dd class="mt-1 font-semibold">10:30</dd></div>
                        <div><dt class="text-terrain-secondary">Místo</dt><dd class="mt-1 font-semibold">Lovčičky</dd></div>
                    </dl>
                    <div class="mt-5 flex gap-2"><x-ui.badge>KT</x-ui.badge><x-ui.badge>OB</x-ui.badge><x-ui.badge>ABM</x-ui.badge></div>
                </div>
                <div class="mt-8 grid gap-10 md:grid-cols-[2fr_1fr]">
                    <div>
                        <h4 class="text-lg font-bold">Informace</h4>
                        <p class="mt-3 max-w-xl leading-relaxed text-terrain-secondary">Zveme vás na závod v okolí Lovčiček. Vše podstatné najdete na jednom místě — od centra závodu po kategorie a dopravu.</p>
                        <dl class="mt-6 grid grid-cols-2 border-y border-terrain-line py-5">
                            <div><dt class="text-sm text-terrain-secondary">Přihlášení</dt><dd class="mt-1 text-3xl font-bold tabular-nums">4</dd></div>
                            <div><dt class="text-sm text-terrain-secondary">Kategorie</dt><dd class="mt-1 text-3xl font-bold tabular-nums">28</dd></div>
                        </dl>
                    </div>
                    <aside class="border-terrain-line md:border-l md:pl-8" aria-label="Kategorie a doprava">
                        <h4 class="text-lg font-bold">Kategorie</h4>
                        <div class="mt-4 flex flex-wrap gap-2">@foreach (['D10', 'D12', 'D14', 'H10', 'H12', 'OPEN'] as $category)<x-ui.badge>{{ $category }}</x-ui.badge>@endforeach</div>
                        <div class="mt-8 rounded-terrain-panel border border-terrain-line bg-terrain-surface p-5"><h4 class="font-bold">Sdílená doprava</h4><p class="mt-2 text-sm text-terrain-secondary">Zatím nikdo nenabízí volné místo.</p></div>
                    </aside>
                </div>
            </section>
        </div>
    </main>
    <footer class="border-t border-terrain-line"><div class="mx-auto flex max-w-terrain flex-wrap justify-between gap-3 px-6 py-6 text-sm text-terrain-secondary"><p>DALIN · Terrain / frontend</p><a href="/" class="font-semibold text-terrain-ink hover:underline">Zpět na web →</a></div></footer>
</body>
</html>

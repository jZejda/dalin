@extends('layouts.terrain')

@section('title', 'Orientace spojuje')

@section('content')
    <section class="terrain-hero">
        <div class="mx-auto flex min-h-[25rem] max-w-terrain flex-col justify-center px-4 py-12 sm:px-6 lg:py-16">
            <h1 class="max-w-lg text-terrain-display font-extrabold">Orientace<br>spojuje</h1>
            <p class="mt-5 max-w-md text-lg leading-relaxed">Společný směr, nové zážitky a pohyb v přírodě. Novinky z klubu i akce, na kterých se potkáme.</p>
            <div class="mt-6"><x-ui.button href="{{ url('/stranka/o-klubu') }}">Zjistit více o klubu <span aria-hidden="true">→</span></x-ui.button></div>
        </div>
    </section>
    <div class="mx-auto max-w-terrain px-4 sm:px-6">
        <section id="novinky" class="py-terrain-section">
            <x-ui.section-heading>Novinky</x-ui.section-heading>
            <livewire:frontend.post-cards :terrain="true" />
        </section>
        <section id="kalendar" class="py-terrain-section">
            <x-ui.section-heading>Nadcházející akce<x-slot:action><a href="#mapa" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold hover:underline">Prohlédnout na mapě <span aria-hidden="true">↓</span></a></x-slot:action></x-ui.section-heading>
            <livewire:frontend.event-list :terrain="true" />
            <div id="mapa" class="mt-10">
                <h3 class="mb-4 text-lg font-bold">Kam vyrazíme</h3>
                <div class="relative isolate overflow-hidden rounded-terrain-panel border border-terrain-line" role="region" aria-label="Mapa nadcházejících akcí">
                    @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, ['publicMap' => true])
                </div>
            </div>
        </section>
    </div>
    <section class="terrain-contours border-y border-terrain-line bg-terrain-muted">
        <div class="mx-auto grid max-w-terrain gap-8 px-4 py-12 sm:px-6 md:grid-cols-2 md:items-center">
            <div><p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-terrain-secondary">Více času na to podstatné</p><h2 class="text-4xl font-extrabold tracking-tight">DALIN</h2><p class="mt-3 text-xl font-bold">Informační systém<br>pro orientační kluby</p></div>
            <div><p class="max-w-lg leading-relaxed text-terrain-secondary">Závody, přihlášky, výsledky i lidé na jednom místě. Pro členy a pořadatele, kteří chtějí trávit více času v terénu.</p><div class="mt-6"><x-ui.button href="https://docs.dalin.cz">Prozkoumat DALIN <span aria-hidden="true">→</span></x-ui.button></div></div>
        </div>
    </section>
    <section class="mx-auto max-w-terrain px-4 py-12 sm:px-6" aria-labelledby="partners-heading">
        <h2 id="partners-heading" class="mb-6 text-sm font-semibold uppercase tracking-widest text-terrain-secondary">Partneři</h2>
        <div class="terrain-partners max-w-xl">@include('partials.frontend.partner-logos-vertical')</div>
    </section>
@endsection

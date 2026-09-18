<header class="relative z-20 border-b border-terrain-line bg-terrain-nav text-terrain-on-nav" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="mx-auto flex max-w-terrain flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <x-ui.brand class="focus-visible:outline-terrain-accent!" />
        <button type="button" class="inline-flex min-h-11 items-center gap-2 rounded-terrain-control border border-terrain-line px-4 text-sm font-semibold focus-visible:outline-terrain-accent! lg:hidden" @click="open = !open" :aria-expanded="open.toString()" aria-expanded="false" aria-controls="terrain-navigation">
            Menu <span aria-hidden="true" x-text="open ? '−' : '+'">+</span>
        </button>
        <nav id="terrain-navigation" aria-label="Hlavní navigace" class="hidden w-full flex-col gap-4 lg:flex lg:w-auto lg:flex-row lg:items-center" :class="{ 'hidden': !open, 'flex': open }">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-semibold" @click="if ($event.target.closest('a')) open = false">
                <a href="{{ url('/#novinky') }}" class="inline-flex min-h-11 items-center hover:text-terrain-accent focus-visible:outline-terrain-accent!">Novinky</a>
                <a href="{{ url('/#kalendar') }}" class="inline-flex min-h-11 items-center hover:text-terrain-accent focus-visible:outline-terrain-accent!">Kalendář</a>
                <a href="{{ url('/stranka/o-klubu') }}" class="inline-flex min-h-11 items-center hover:text-terrain-accent focus-visible:outline-terrain-accent!">Klub</a>
                <a href="{{ url('/stranka/poradane-zavody') }}" class="inline-flex min-h-11 items-center hover:text-terrain-accent focus-visible:outline-terrain-accent!">Závody</a>
            </div>
            <x-ui.theme-select />
            <x-ui.button href="{{ url('/admin') }}" class="focus-visible:outline-terrain-accent!">{{ auth()->check() ? 'Členská sekce' : 'Přihlásit se' }}</x-ui.button>
        </nav>
    </div>
</header>

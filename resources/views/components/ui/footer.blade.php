<footer class="terrain-footer text-terrain-on-nav">
    <div class="mx-auto flex max-w-terrain flex-col gap-10 px-4 pb-8 sm:px-6 lg:flex-row lg:items-start lg:justify-between">
        <div><x-ui.brand size="lg" class="focus-visible:outline-terrain-accent!" /><p class="mt-4 text-lg font-semibold sm:text-xl">Orientace nás posouvá dál.</p></div>
        <div>
            <div class="grid grid-cols-2 gap-x-6 gap-y-8 lg:gap-x-20">
                <div><h2 class="mb-4 text-lg font-extrabold sm:text-xl">Klub</h2><ul class="space-y-3 text-lg font-semibold sm:text-xl"><li><a href="{{ url('/stranka/o-klubu') }}" class="hover:underline focus-visible:outline-terrain-accent!">Informace o klubu</a></li><li><a href="{{ url('/stranka/poradane-zavody') }}" class="hover:underline focus-visible:outline-terrain-accent!">Pořádané závody</a></li><li><a href="{{ url('/#kalendar') }}" class="hover:underline focus-visible:outline-terrain-accent!">Kalendář a mapa</a></li><li><a href="https://mapy.orientacnisporty.cz/cs/clubs/{{ strtolower((string) config('site-config.club.abbr')) }}" class="hover:underline focus-visible:outline-terrain-accent!">Naše mapy</a></li></ul></div>
                <div><h2 class="mb-4 text-lg font-extrabold sm:text-xl">Orientační sporty</h2><ul class="space-y-3 text-lg font-semibold sm:text-xl"><li><a href="http://zhusta.sky.cz/Jihomoravska_oblast" class="hover:underline focus-visible:outline-terrain-accent!">Jihomoravská oblast</a></li><li><a href="http://www.orientacnibeh.cz/" class="hover:underline focus-visible:outline-terrain-accent!">Orientační běh</a></li><li><a href="http://www.obpostupy.cz/" class="hover:underline focus-visible:outline-terrain-accent!">OB postupy</a></li><li><a href="{{ url('/stranka/odkazy') }}" class="hover:underline focus-visible:outline-terrain-accent!">Další odkazy</a></li></ul></div>
            </div>
            <p class="mt-10 text-base font-semibold">© {{ date('Y') }} {{ config('site-config.club.full_name') }} · Informační systém <a href="https://docs.dalin.cz" class="underline focus-visible:outline-terrain-accent!">DALIN</a></p>
        </div>
    </div>
</footer>

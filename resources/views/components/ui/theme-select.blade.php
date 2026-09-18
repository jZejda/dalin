@props(['id' => 'terrain-theme'])
<div class="flex items-center gap-2 text-sm">
    <label for="{{ $id }}">Vzhled</label>
    <select id="{{ $id }}" data-terrain-theme {{ $attributes->class(['min-h-11 max-w-full rounded-terrain-control border border-terrain-line bg-terrain-nav px-2 text-terrain-on-nav focus-visible:outline-terrain-accent!']) }}>
        <option value="system">Podle systému</option><option value="light">Světlý</option><option value="dark">Tmavý</option>
    </select>
</div>

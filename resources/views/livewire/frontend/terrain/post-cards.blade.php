<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($posts as $post)
        <x-ui.post-card :post="$post" wire:key="terrain-post-{{ $post->id }}" />
    @empty
        <p class="text-sm text-terrain-secondary md:col-span-2 lg:col-span-3">Zatím tu nejsou žádné novinky. Brzy se dozvíte, co se v klubu děje.</p>
    @endforelse
</div>

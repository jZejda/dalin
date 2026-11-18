@props(['credit'])

@php
    use App\Shared\Helpers\AppHelper;
    use App\Shared\Helpers\Markdown;

    /** @var \App\Models\UserCredit $credit */

    // Internal notes (e.g. raw ORIS payloads) are not comments — same rule as UserCredit::userCreditNoteCount().
    $notes = $credit->userCreditNotes->where('internal', false)->sortBy('created_at')->values();

    // Consecutive notes by the same author share one header, like in a chat / notes thread.
    $groups = [];
    foreach ($notes as $note) {
        $last = array_key_last($groups);
        if ($last !== null && $groups[$last]['author_id'] === $note->note_user_id) {
            $groups[$last]['notes'][] = $note;
        } else {
            $groups[] = ['author_id' => $note->note_user_id, 'author' => $note->userNoteMade, 'notes' => [$note]];
        }
    }
@endphp

@if ($notes->isNotEmpty())
    <div class="inline-block" {{ $attributes }}>
        {{-- Hover content: Filament's x-tooltip (Tippy) handles hover/focus, placement, flipping and cleanup. --}}
        <template x-ref="panel">
            <div class="max-h-[min(24rem,70vh)] w-[min(26rem,calc(100vw-3rem))] overflow-y-auto p-1 text-left text-sm">
                <div class="space-y-3">
                    @foreach ($groups as $group)
                        @php($author = $group['author'])
                        <div class="flex gap-2">
                            @if ($author)
                                <x-user-badge
                                    :initials="$author->initials"
                                    :color="$author->badge_color"
                                    :avatar-url="$author->avatar_url"
                                    size="xs"
                                    class="mt-0.5"
                                />
                            @else
                                <x-user-badge initials="N/A" size="xs" class="mt-0.5" />
                            @endif

                            <div class="min-w-0 flex-1 space-y-1.5">
                                <div class="font-semibold">
                                    {{ $author?->name ?? __('user-credit.notes_popover.unknown_author') }}
                                </div>

                                @foreach ($group['notes'] as $note)
                                    <div class="rounded-lg bg-gray-100 px-3 py-2 text-gray-700 dark:bg-white/10 dark:text-gray-200">
                                        <div class="app-front-content break-words">
                                            {{ Markdown::userText($note->note) }}
                                        </div>
                                        <time class="mt-1 block text-xs text-gray-400 dark:text-gray-400">
                                            {{ $note->created_at?->format(AppHelper::DATE_TIME_FORMAT) }}
                                        </time>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </template>

        {{-- A span, not a <button>: Filament wraps cells of clickable rows in a <button>, and nested buttons are invalid HTML. --}}
        <span
            role="button"
            tabindex="0"
            x-data
            x-tooltip="{
                content: $el.parentElement.querySelector('template').content.firstElementChild.cloneNode(true),
                allowHTML: true,
                interactive: true,
                maxWidth: 'none',
                delay: [150, 200],
                placement: 'bottom-start',
                appendTo: () => document.body,
                theme: $store.theme,
            }"
            class="relative inline-flex items-center rounded-md p-1 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400"
            aria-label="{{ __('user-credit.notes_popover.aria_label', ['count' => $notes->count()]) }}"
        >
            <x-filament::icon :icon="\CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon::MessageCircleMore" class="h-6 w-6" />
            <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-primary-600 px-1 text-[10px] font-semibold leading-none text-white">{{ $notes->count() }}</span>
        </span>
    </div>
@endif

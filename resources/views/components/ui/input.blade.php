@props(['id', 'label', 'hint' => null, 'error' => null])
<div>
    <label for="{{ $id }}" class="mb-2 block text-sm font-semibold">{{ $label }}</label>
    <input id="{{ $id }}" @if($hint || $error) aria-describedby="{{ $id }}-description" @endif @if($error) aria-invalid="true" @endif {{ $attributes->merge(['type' => 'text'])->class(['min-h-11 w-full rounded-terrain-control border bg-terrain-surface px-3 py-2 text-base disabled:bg-terrain-muted', 'border-terrain-danger' => (bool) $error, 'border-terrain-line' => !$error]) }}>
    @if($hint || $error)<p id="{{ $id }}-description" @class(['mt-2 text-sm', 'text-terrain-danger' => (bool) $error, 'text-terrain-secondary' => !$error])>{{ $error ?: $hint }}</p>@endif
</div>

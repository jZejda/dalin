@php
    use App\Enums\DocumentCategory;
    use App\Models\Document;
    use Illuminate\Support\Collection;

    /** @var Collection<int, array{category: DocumentCategory, documents: Collection<int, Document>}> $sections */
    $fileIcon = static fn (?string $mime): string => match (true) {
        $mime === null => 'lucide-file',
        str_starts_with($mime, 'image/') => 'lucide-file-image',
        str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel') => 'lucide-file-spreadsheet',
        default => 'lucide-file-text',
    };
    $fileFormat = static fn (?string $fileName): string => strtoupper(pathinfo((string) $fileName, PATHINFO_EXTENSION));
@endphp

@extends('layouts.terrain')

@section('title', __('document.public.title'))

@section('content')
    <section class="terrain-contours">
        <div class="mx-auto max-w-terrain px-4 py-8 sm:px-6 md:py-12">
            <nav aria-label="{{ __('content.post.public.breadcrumb_label') }}" class="mb-6 flex min-w-0 flex-wrap items-center gap-2 text-sm text-terrain-secondary">
                <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('content.post.public.breadcrumb_home') }}</a><span aria-hidden="true">/</span>
                <span aria-current="page">{{ __('document.public.title') }}</span>
            </nav>
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">{{ __('document.public.title') }}</h1>
            <p class="mt-4 max-w-xl text-lg text-terrain-secondary">{{ __('document.public.lead') }}</p>
        </div>
    </section>

    <div class="mx-auto max-w-terrain px-4 pb-terrain-section pt-8 sm:px-6 md:pt-10">
        @forelse ($sections as $section)
            <section class="mb-12 last:mb-0" aria-labelledby="documents-{{ $section['category']->value }}">
                <x-ui.section-heading id="documents-{{ $section['category']->value }}">{{ $section['category']->getLabel() }}</x-ui.section-heading>

                <ul class="divide-y divide-terrain-line overflow-hidden rounded-terrain-panel border border-terrain-line bg-terrain-surface" role="list">
                    @foreach ($section['documents'] as $document)
                        @php($file = $document->file())
                        <li class="group relative flex min-w-0 items-start gap-4 p-4 transition-colors hover:bg-terrain-muted motion-reduce:transition-none sm:p-5">
                            @svg($fileIcon($file?->mime_type), 'mt-0.5 size-6 shrink-0 text-terrain-secondary', ['aria-hidden' => 'true'])
                            <div class="min-w-0 flex-1">
                                <h3 class="break-words font-bold leading-snug">
                                    <a href="{{ $document->publicUrl() }}" class="decoration-terrain-accent underline-offset-4 after:absolute after:inset-0 group-hover:underline">{{ $document->title }}</a>
                                </h3>
                                @if ($document->description)
                                    <p class="mt-1 text-sm text-terrain-secondary">{{ $document->description }}</p>
                                @endif
                                <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-terrain-secondary">
                                    @if ($document->year)<span class="font-semibold text-terrain-ink">{{ $document->year }}</span><span aria-hidden="true">·</span>@endif
                                    <span>{{ $fileFormat($file?->file_name) }}</span><span aria-hidden="true">·</span>
                                    <span>{{ $file?->human_readable_size }}</span>
                                    @unless ($document->is_public)
                                        <x-ui.badge tone="warning" class="ml-1">{{ __('document.public.members_only') }}</x-ui.badge>
                                    @endunless
                                </p>
                            </div>
                            @svg('lucide-download', 'mt-0.5 size-5 shrink-0 text-terrain-ink', ['aria-hidden' => 'true'])
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <p class="text-sm text-terrain-secondary">{{ __('document.public.empty') }}</p>
        @endforelse

        @guest
            <p class="mt-10 text-sm text-terrain-secondary">
                {{ __('document.public.members_hint') }}
                <a href="{{ route('filament.admin.auth.login') }}" class="font-semibold text-terrain-ink underline underline-offset-4">{{ __('document.public.members_login') }}</a>
            </p>
        @endguest
    </div>
@endsection

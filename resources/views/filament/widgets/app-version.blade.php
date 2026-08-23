<x-filament::widget>
    <x-filament::card>
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2 3 7v10l9 5 9-5V7z"/><path d="M3 7l9 5 9-5"/><path d="M12 12v10"/>
            </svg>

            <div class="min-w-0">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('dashboard.version.heading') }}
                </div>

                <div class="mt-1 text-lg font-bold tracking-tight">
                    {{ $appName }}
                    <span class="text-primary-600 dark:text-primary-400">{{ $tag }}</span>
                </div>

                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    <span class="font-mono">{{ $buildLabel }}</span>
                    @if($deployedAt)
                        <span aria-hidden="true">&middot;</span>
                        <span>{{ $deployedAt }}</span>
                    @endif
                </div>

                <div class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $runtime }}
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>

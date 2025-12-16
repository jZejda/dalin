<div class="space-y-4">
    @if($hasApiKey && $apiKey)
        <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-success-800 dark:text-success-200 mb-2">
                        API klíč hash
                    </h3>
                    <p class="text-sm text-success-700 dark:text-success-300 mb-3">
                        Toto je hash vašeho API klíče. Použijte tento hash v hlavičce <code class="bg-white dark:bg-gray-800 px-1 py-0.5 rounded">x-apikey</code> při volání API. Hash zůstane viditelný i po obnovení stránky.
                    </p>
                    <div class="flex gap-2">
                        <div class="flex-1 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg p-3 font-mono text-sm break-all">
                            {{ $apiKey }}
                        </div>
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $apiKey }}'); $wire.call('copyApiKey')"
                            class="flex-shrink-0 px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($hasApiKey)
        <div class="space-y-3">
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        Aktivní API klíč
                    </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Máte aktivní API klíč. Hash klíče je zobrazen výše. Můžete jej přegenerovat (starý klíč přestane fungovat) nebo smazat.
                </p>
            </div>

            <div class="flex gap-3">
                <button
                    type="button"
                    wire:click="regenerateApiKey"
                    wire:confirm="Opravdu chcete přegenerovat API klíč? Starý klíč přestane fungovat."
                    class="flex-1 px-4 py-2 bg-warning-600 hover:bg-warning-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Přegenerovat klíč
                </button>

                <button
                    type="button"
                    wire:click="deleteApiKey"
                    wire:confirm="Opravdu chcete smazat API klíč? Tuto akci nelze vrátit zpět."
                    class="flex-1 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Smazat klíč
                </button>
            </div>
        </div>
    @else
        <div class="space-y-3">
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        Žádný API klíč
                    </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Nemáte vygenerovaný API klíč. Vygenerujte si jej pro přístup k API.
                </p>
            </div>

            <button
                type="button"
                wire:click="generateApiKey"
                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Vygenerovat API klíč
            </button>
        </div>
    @endif

    <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg p-4">
        <div class="flex gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-medium text-info-800 dark:text-info-200 mb-1">
                    Důležité informace
                </h4>
                <ul class="text-sm text-info-700 dark:text-info-300 space-y-1 list-disc list-inside">
                    <li>Hash API klíče je zobrazen výše a zůstane viditelný i po obnovení stránky</li>
                    <li>Použijte hash v hlavičce <code class="bg-white dark:bg-gray-800 px-1 py-0.5 rounded">x-apikey</code> při volání API</li>
                    <li>Uchovávejte hash v tajnosti a nesdílejte jej s nikým</li>
                    <li>Klíč můžete kdykoliv přegenerovat nebo smazat</li>
                    <li>Po přegenerování přestane starý klíč fungovat</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="space-y-4">
    @if($this->has_calendar_token)
        <div class="space-y-3">
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        Soukromé kalendáře
                    </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Máte aktivní token pro přístup k iCalendar kanálům. Používejte níže uvedené adresy v aplikacích, které podporují iCalendar formát.
                </p>

                <div class="space-y-3 mb-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">
                            Kanál závodů (races)
                        </label>
                        <div class="flex gap-2">
                            <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 font-mono text-xs break-all">
                                {{ url('/api/feed/kalendar/zavody/me') }}/{{ $this->calendar_token }}
                            </div>
                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ url('/api/feed/kalendar/zavody/me') }}/{{ $this->calendar_token }}'); $wire.call('copyCalendarUrl')"
                                class="flex-shrink-0 px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">
                            Kanál tréninků (trainings)
                        </label>
                        <div class="flex gap-2">
                            <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 font-mono text-xs break-all">
                                {{ url('/api/feed/kalendar/treninky/me') }}/{{ $this->calendar_token }}
                            </div>
                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ url('/api/feed/kalendar/treninky/me') }}/{{ $this->calendar_token }}'); $wire.call('copyCalendarUrl')"
                                class="flex-shrink-0 px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        wire:click="regenerateCalendarToken"
                        wire:confirm="Opravdu chcete přegenerovat token? Starý token přestane fungovat a všechny odkazy na kanály budou neplatné."
                        class="flex-1 px-4 py-2 bg-warning-600 hover:bg-warning-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Přegenerovat token
                    </button>

                    <button
                        type="button"
                        wire:click="revokeCalendarToken"
                        wire:confirm="Opravdu chcete zrušit token? Všechny odkazy na kanály budou neplatné."
                        class="flex-1 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Zrušit token
                    </button>
                </div>
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
                        Žádný kalendářový token
                    </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Nemáte vygenerovaný token. Vygenerujte si jej pro přístup k vašim osobním iCalendar kanálům.
                </p>
            </div>

            <button
                type="button"
                wire:click="generateCalendarToken"
                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Vygenerovat token
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
                    <li>Token slouží pro přístup k vašim osobním iCalendar kanálům</li>
                    <li>Adresy kanálů jsou vždy viditelné, dokud máte aktivní token</li>
                    <li>Token můžete kdykoliv přegenerovat nebo zrušit</li>
                    <li>Po přegenerování přestane starý token fungovat</li>
                    <li>Používejte odkaz na kanál v aplikacích podporujících iCalendar (např. Google Calendar, Apple Calendar, Outlook)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

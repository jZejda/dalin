<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="app-front-content">
            <div>
                <p class="text-gray-500 sm:text-lg dark:text-gray-400">Členský vklad</p>
                <ul>
                    <li>na účet: <strong>{!!  Config::get('site-config.club.primary_bank_account_number') !!}</strong><span class="tracking-tight"> vedený u {!!  Config::get('site-config.club.primary_bank_account_name') !!}</span> </li>
                    <li>VS použijte: <strong>{!!  Config::get('site-config.club.extra_membership_fees_prefix') !!}</strong> <span class="tracking-tight"> + reg. číslo člena bez {!!  Config::get('site-config.club.abbr') !!}</span> </li>
                </ul>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>

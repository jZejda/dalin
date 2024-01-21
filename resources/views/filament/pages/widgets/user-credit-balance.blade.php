<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="app-front-content">
            <div class="grid grid-cols-2 gap-4 mb-1">
                <div>
                    <p class="text-gray-500 sm:text-lg dark:text-gray-400">Aktuální stav</p>
                    <div class="flex items-baseline my-4">
                        <span class="mr-2 text-5xl font-extrabold">{{$user_balance}}</span>
                        <span class="text-gray-500 dark:text-gray-400">Kč</span>

                    </div>
                </div>
                <div>
                    <div class="flex justify-end">
                        @if(Auth::user()->payer_variable_symbol !== null)
                        <div>{!! QrCode::size(120)->generate('SPD*1.0*RN:KLUB ORIENTACNIHO BEHU ALFA BRNO Z.S.*ACC:' . config('site-config.club.iban') . '*CC:CZK*X-VS:888' . Auth::user()->payer_variable_symbol . '*MSG:MIMORADNY CLENSKY VKLAD') !!}</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>

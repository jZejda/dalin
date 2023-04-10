@php
    use App\Models\UserCredit;
    use App\Models\UserCreditNote;
    use App\Shared\Helpers\EmptyType;
    use Illuminate\Support\Carbon;

    /** @var UserCredit $record */
@endphp

<x-filament::widget>
    <x-filament::card>

        @php
            /** @var UserCreditNote $orisApiResponse */
            $orisApiResponse = $record->userCreditInternalOrisNote($record->id);
        @endphp

        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">ORIS API</h2>
        <p class="text-gray-500 dark:text-gray-400">Data o přihlášce, která vrátil ORIS při prvním načtení nákladů.</p>

        @if(!is_null($orisApiResponse))
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                <li>
                    Registrační číslo: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['regNo'] }}</span>
                </li>
                <li>
                    Poplatek: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['fee'] }}</span>
                </li>
                <li>
                    Jméno: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['name'] }}</span>
                </li>
                <li>
                    Poznámka: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['note'] }}</span>
                </li>
                <li>
                    Požeadavek na půjčení čipu: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['rentSI'] }}</span>
                </li>
                <li>
                    ID uživatele v ORISu: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['userID'] }}</span>
                </li>
                <li>
                    Kategorie závodu: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['classDesc'] }}</span>
                </li>
                <li>
                    ORIS ID přihlášky: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['id'] }}</span>
                </li>
                <li>
                    Přihlásil uživatel ID: <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['CreatedByUserID'] }}</span>
                </li>
                <li>
                    Přihlášeno: <span class="font-semibold text-gray-900 dark:text-white">
                    {{ Carbon::createFromFormat('Y-m-d H:i:s', $orisApiResponse->params['CreatedDateTime'])->format('d. m. Y H:i') }}
                </span>
                </li>
                @if(EmptyType::stringNotEmpty($orisApiResponse->params['UpdatedByUserID']))
                    <li>
                        Upraveno uživatelem: <span
                            class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['UpdatedByUserID'] }}</span>
                    </li>
                @endif
                @if(EmptyType::stringNotEmpty($orisApiResponse->params['UpdatedDateTime']))
                    <li>
                        Upravil uživatel ID: <span
                            class="font-semibold text-gray-900 dark:text-white">{{ $orisApiResponse->params['UpdatedDateTime'] }}</span>
                    </li>
                @endif
            </ul>
        @else
            <span class="flex items-center text-sm font-medium text-gray-900 dark:text-white"><span
                    class="flex w-2.5 h-2.5 bg-yellow-300 rounded-full mr-1.5 flex-shrink-0"></span>Z ORISu nemám žádná data.</span>
        @endif
        {{-- Widget content --}}
    </x-filament::card>
</x-filament::widget>

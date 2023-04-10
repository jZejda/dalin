@php
    use App\Models\UserCredit;
    use App\Models\UserCreditNote;
    use App\Shared\Helpers\EmptyType;
    use Illuminate\Support\Carbon;

    /** @var UserCredit $record */
@endphp

<x-filament::widget>
    <x-filament::card>
        <div class="be-widget">

        <ol class="relative border-l border-gray-200 dark:border-gray-700">

        @foreach ($record->userCreditNotes()->get() as $note)

            @php
                $userName = $note->userNoteMade->name;
            @endphp

            @if (Auth::user()->hasRole(['super_admin']) && !$note->private )
                @if ($loop->last)
                    <li class="ml-4">
                @else
                    <li class="mb-20 ml-4 border-b border-gray-200">
                @endif
{{--                        <div class="absolute w-3 h-3 bg-yellow-400 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-yellow-400"></div>--}}
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ $note->created_at->format('d.m.Y H:i') }}
                        </time>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{$userName}}</h4>
                        <p class="app-front-content">
                            <span>{!! \Illuminate\Support\Str::markdown($note->note ?? '') !!}</span>
                        </p>
                    </li>
            @else
                @if ($loop->last)
                    <li class="ml-4">
                @else
                    <li class="mb-10 ml-4">
                        @endif
{{--                        <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>--}}
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ $note->created_at->format('d.m.Y H:i') }}
                        </time>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{$userName}}</h4>
                        <p class="app-front-content">
                            <span>{!! \Illuminate\Support\Str::markdown($note->note ?? '') !!}</span>
                        </p>
                    </li>

            @endif
        @endforeach
        </ol>
        </div>

        {{-- Widget content --}}
    </x-filament::card>
</x-filament::widget>

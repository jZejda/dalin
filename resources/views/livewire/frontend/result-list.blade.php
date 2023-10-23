@php
    /** @var string|null $searchCategory */
    /** @var string $eventName */
    /** @var App\Http\Components\Iofv3\Entities\ClassResult[] $classResult */
    /** @var App\Models\SportEventExport $sportEventExport */
    /** @var App\Http\Components\Iofv3\Entities\Attributes[] $eventAttributes */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="mb-2 md:mb-6 py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
    <div id="title" class="container mx-auto">
        @if(!is_null($classResult) && count($eventAttributes) > 0)
        <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 text-transparent bg-clip-text font-extrabold">
            {{ $eventName }}
        </div>
        <div>
            <div class="mt-0 pt-0 ml-3">
                <span class="text-gray-400 text-md font-normal"> {{\Carbon\Carbon::parse($eventAttributes[0]->getCreateTime(), 'Europe/Prague')->format('d.m.Y - H:i')}} | </span>
                <span class="text-gray-400 text-md font-normal">{{$eventAttributes[0]->getCreator()}} </span>
            </div>
        </div>
        @else
            <div class="text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 text-transparent bg-clip-text font-extrabold">
                404 Nenalezeno
            </div>
        @endif
    </div>
</div>
<div>
    <div class="p-4 bg-white dark:bg-gray-900">
        <div class="container mx-auto ">
            @if(!is_null($classResult) && count($eventAttributes) > 0)
                {{--            <h4>{{$search}}</h4>--}}

                {{--            <div>--}}
                {{--                <label>Label</label>--}}
                {{--                <input wire:model="message" type="text" name="message" value="Hello">--}}
                {{--            </div>--}}
                {{--            <div>{{$message}}</div>--}}
                {{--            <div>@json($message)</div>--}}

                {{--            <label for="search"></label><select id="search" wire:model="search">--}}
                {{--                <option value="D10">D10</option>--}}
                {{--                <option value="D12">D12</option>--}}
                {{--            </select>--}}

                {{--            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select an option</label>--}}
                {{--            <select wire:model="search" id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
                {{--                <option selected>Choose a country</option>--}}
                {{--                <option value="D10">D10</option>--}}
                {{--                <option value="D12">D12</option>--}}
                {{--            </select>--}}

                {{--            <div>Select: @json($search)</div>--}}



                @foreach($classResult as $class)
                    <a href="{{url()->current()}}#{{$class->getClass()->getName()}}" class="text-gray-800 bg-yellow-300 hover:bg-yellow-400 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center dark:hover:bg-yellow-100 mr-1 mb-1">
                        {{ $class->getClass()->getName() }}
                    </a>
                @endforeach
                @foreach($classResult as $class)
                    <section class="app-front-content mt-5 sm:py-5">
                        <div class="mx-auto max-w-screen-2xl">
                            <div class="relative overflow-hidden bg-white shadow-lg dark:bg-gray-800 sm:rounded-lg">
                                <div class="text-gray-800 bg-yellow-300 flex flex-col px-4 pb-2 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                                    <div class="flex items-center flex-1 space-x-4">
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-800">Kategorie</span>
                                            <a id="{{$class->getClass()->getName()}}">
                                                <span class="text-gray-800 decoration-yellow-300 underline">{{ $class->getClass()->getName() }}</span>
                                            </a>
                                        </h5>
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-800">Prevýšení:</span>
                                            <span class="text-gray-800">{{ $class->getCourse()->getClimb() }}<span class="font-light"> m</span></span>
                                        </h5>
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-800">Vzdálenost:</span>
                                            <span class="text-gray-800">{{ number_format((float)$class->getCourse()->getLength() / 1000, 2, '.', '') }}<span class="font-light"> km</span></span>
                                        </h5>
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-800">Kontrol:</span>
                                            <span class="text-gray-800">{{ $class->getCourse()->getNumberOfControls() }}</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left text-gray-800 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">#</th>
                                            <th class="px-4 py-3">Jméno</th>
                                            <th class="hidden lg:block px-4 py-3">Klub</th>
                                            <th class="px-4 py-3">Reg. číslo</th>
                                            <th class="hidden lg:block px-4 py-3">Start</th>
                                            <th class="px-4 py-3">Cíl</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($class->getPersonResult() as $person)
                                            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <td class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{ $person->getResult()->getPosition() }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    @if($person->getPerson()->getName()->getFamily() === 'Vakant' || $person->getPerson()->getName()->getGiven() === 'Vakant')
                                                        <span class="bg-orange-400 text-gray-800 text-xs font-medium px-2 py-0.5 rounded">
                                                        {{$person->getPerson()->getName()->getFamily()}}
                                                        </span>
                                                    @else
                                                        <span class="text-black font-medium py-0.5 dark:text-white">
                                                        {{$person->getPerson()->getName()->getFamily()}} {{$person->getPerson()->getName()->getGiven()}}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="hidden lg:block  px-4 py-2 text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{$person->getOrganisation()->getName()}}
                                                </td>
                                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <div class="flex items-center">
                                                        @if(\App\Shared\Helpers\EmptyType::arrayNotEmpty($person->getPerson()->getId()))
                                                            {{$person->getPerson()->getId()[0]}}
                                                        @else
                                                            {{$person->getPerson()->getId()}}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="hidden lg:block px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <div class="flex items-center">
                                                        @php
                                                            $startTime = \Carbon\Carbon::parse($person->getResult()->getStartTime(), 'Europe/Prague');
                                                            $zeroTime = $startTime->diffInSeconds($sportEventExport->start_time);
                                                        @endphp
                                                        {{\Carbon\Carbon::parse($person->getResult()->getStartTime(), 'Europe/Prague')->format('H:i:s')}}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <div class="flex items-center">
                                                        @php
                                                            $secondsAll = (int)$person->getResult()->getTime();
                                                            $minutes = floor($secondsAll / 60);
                                                            $seconds = ($secondsAll - ($minutes * 60));

                                                            $secondsBehindAll = (int)$person->getResult()->getTimeBehind();
                                                            $minutesBehind = floor($secondsBehindAll / 60);
                                                            $secondsBehind = ($secondsBehindAll - ($minutesBehind * 60));
                                                        @endphp

                                                        <span class="text-lg tracking-tight">
                                                            {{sprintf("%02d", $minutes)}}:{{sprintf("%02d", $seconds)}}
                                                        </span>
                                                        @if((int)$person->getResult()->getTimeBehind() !== 0)
                                                            <span class="ml-2 text-gray-500 text-xs tracking-tight">
                                                                +{{sprintf("%02d", $minutesBehind)}}:{{sprintf("%02d", $secondsBehind)}}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <nav class="flex flex-col items-start justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0" aria-label="Table navigation">
                                    <a href="{{url()->current()}}#title">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up-circle"><circle cx="12" cy="12" r="10"/><polyline points="16 12 12 8 8 12"/><line x1="12" y1="16" x2="12" y2="8"/></svg>
                                    </a>
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                        Celkem v kategorii:
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                         {{count($class->getPersonResult())}}
                                    </span>
                                        závodníků.
                                    </span>
                                </nav>
                            </div>
                        </div>
                    </section>
                @endforeach

            @else
                <section class="app-front-content">
                    <div
                        class="mb-4 rounded-lg bg-success-100 px-6 py-5 text-base text-success-700"
                        role="alert">
                        <h4 class="mb-2 text-2xl font-medium leading-tight">Oops!</h4>
                        <p class="mb-4">
                            Zatím zde není žádný obsah. Výsledky pravděpodobně ještě nejsou dostupné, nebo se na nich usilovně pracuje.
                            Zkontrolujte zdali se jedná o výsledky zveřejněné k tomuto závodu.
                        </p>
                        <hr class="border-success-600 opacity-30" />
                        <p class="mb-0 mt-4">
                            Počkejte na zveřejnění. Předem děkujeme.
                        </p>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>

@endsection

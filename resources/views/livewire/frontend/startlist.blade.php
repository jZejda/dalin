@php
    /** @var string|null $searchCategory */
    /** @var string $eventName */
    /** @var App\Http\Components\Iofv3\Entities\ClassStart[] $classStart */
    /** @var App\Models\SportEventExport $sportEventExport */
    /** @var App\Http\Components\Iofv3\Entities\Attributes[] $eventAttributes */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div>


    <div class="p-4 bg-white dark:bg-gray-900">
        <div class="container mx-auto ">
        @if(!is_null($classStart) && count($eventAttributes) > 0)
            <div>
                <a id="title" class="text-4xl font-extrabold mb-4 dark:text-white">{{ $eventName }}</a>
            </div>
            <div class="mb-5">
                <span class="text-gray-600 dark:text-gray-400"> {{\Carbon\Carbon::parse($eventAttributes[0]->getCreateTime(), 'Europe/Prague')->format('d.m.Y - H:i')}} | </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{$eventAttributes[0]->getCreator()}} </span>
            </div>
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



            @foreach($classStart as $class)
                <a href="{{url()->current()}}#{{$class->getClass()->getName()}}" class="text-white bg-[#FF9119] hover:bg-[#FF9119]/80 focus:ring-4 focus:outline-none focus:ring-[#FF9119]/50 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center dark:hover:bg-[#FF9119]/80 dark:focus:ring-[#FF9119]/40 mr-1 mb-1">
                    {{ $class->getClass()->getName() }}
                </a>
            @endforeach
            @foreach($classStart as $class)
                <section class="app-front-content mt-5 sm:py-5">
                    <div class="mx-auto max-w-screen-2xl">
                        <div class="relative overflow-hidden bg-white shadow-lg dark:bg-gray-800 sm:rounded-lg">
                            <div class="bg-gradient-to-r bg-orange-400 dark:bg-orange-400 flex flex-col px-4 pb-2 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                                <div class="flex items-center flex-1 space-x-4">
                                    <h5>
                                        <span class="tracking-tight font-light text-gray-700 dark:text-gray-200">Kategorie</span>
                                        <a id="{{$class->getClass()->getName()}}">
                                            <span class="underline decoration-orange-400">{{ $class->getClass()->getName() }}</span>
                                        </a>
                                    </h5>
                                    <h5>
                                        <span class="tracking-tight font-light text-gray-700 dark:text-gray-200">Prevýšení:</span>
                                        <span>{{ $class->getCourse()->getClimb() }}<span class="font-light"> m</span></span>
                                    </h5>
                                    <h5>
                                        <span class="tracking-tight font-light text-gray-700 dark:text-gray-200">Vzdálenost:</span>
                                        <span>{{ number_format((float)$class->getCourse()->getLength() / 1000, 2, '.', '') }}<span class="font-light"> km</span></span>
                                    </h5>
                                    <h5>
                                        <span class="tracking-tight font-light text-gray-700 dark:text-gray-200">Kontrol:</span>
                                        <span>{{ $class->getCourse()->getNumberOfControls() }}</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3">Start</th>
                                        <th class="px-4 py-3">00</th>
                                        <th class="px-4 py-3">Jméno</th>
                                        <th class="px-4 py-3">Reg. číslo</th>
                                        <th class="px-4 py-3">SI čip</th>
                                        <th class=" hidden lg:block px-4 py-3">Klub</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($class->getPersonStart() as $person)
                                        <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{\Carbon\Carbon::parse($person->getStart()->getStartTime(), 'Europe/Prague')->format('H:i:s')}}
                                                {{--                                                {{$person->getStart()->getStartTime()}}--}}
                                            </td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white font-extrabold">
                                                @php
                                                    $startTime = \Carbon\Carbon::parse($person->getStart()->getStartTime(), 'Europe/Prague');
                                                    $zeroTime = $startTime->diffInMinutes($sportEventExport->start_time);
                                                @endphp
                                                {{sprintf("%02d", $zeroTime)}}
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
                                            <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center">
                                                    @if(\App\Shared\Helpers\EmptyType::arrayNotEmpty($person->getPerson()->getId()))
                                                        {{--                                                        {{$person->getPerson()->getId()[0]}}--}}
                                                    @else
                                                        {{$person->getPerson()->getId()}}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{$person->getStart()->getControlCard()}}
                                            </td>
                                            <td class="hidden lg:block  px-4 py-2 text-gray-900 whitespace-nowrap dark:text-white">
                                                {{$person->getOrganisation()->getName()}}
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
                                      {{count($class->getPersonStart())}}
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
                <h1>404 Nenalezeno</h1>
                <p>Zatím zde není žádný obsah. Na startovce se pravděpodobně ještě pracuje. Počkejte na zveřejnění. Předem děkujeme.</p>
            </section>

            @endif

        </div>
    </div>
    </div>

@endsection

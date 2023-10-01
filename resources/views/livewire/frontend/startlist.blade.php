@php
    /** @var string|null $searchCategory */
    /** @var string $eventName */
    /** @var App\Http\Components\Iofv3\Entities\ClassStart[] $classStart */
    /** @var App\Models\SportEventExport $sportEventExport */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div>


    <div class="p-4 bg-white dark:bg-gray-900">
        <div class="container mx-auto app-front-content mb-10">
            <h1>{{ $eventName }}</h1>
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


        @if(!is_null($classStart))
                @foreach($classStart as $class)
                    <section class="sm:py-5">
                        <div class="mx-auto max-w-screen-2xl">
                            <div class="relative overflow-hidden bg-white shadow-lg dark:bg-gray-800 sm:rounded-lg">
                                <div class="bg-gradient-to-r from-lime-400 to-lime-500 dark:bg-gray-800 flex flex-col px-4 pb-2 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                                    <div class="flex items-center flex-1 space-x-4">
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-700">Kategorie</span>
                                            <span class="dark:text-black">{{ $class->getClass()->getName() }}</span>
                                        </h5>
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-700">Prevýšení:</span>
                                            <span class="dark:text-black">{{ $class->getCourse()->getClimb() }}<span class="font-light"> m</span></span>
                                        </h5>
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-700">Vzdálenost:</span>
                                            <span class="dark:text-black">{{ number_format((float)$class->getCourse()->getLength() / 1000, 2, '.', '') }}<span class="font-light"> km</span></span>
                                        </h5>
                                        <h5>
                                            <span class="tracking-tight font-light text-gray-700">Kontrol:</span>
                                            <span class="dark:text-black">{{ $class->getCourse()->getNumberOfControls() }}</span>
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
                                                        <span class="bg-lime-600 text-lime-200 text-xs font-medium px-2 py-0.5 rounded">
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

                <p>no neco se pokazilo</p>
            @endif






        </div>
    </div>
    </div>

@endsection

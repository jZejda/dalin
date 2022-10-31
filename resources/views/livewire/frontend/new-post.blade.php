@extends('layouts.app')

@section('navbar')
    <livewire:frontend.navbar />
@endsection

@section('content')
    <div class="container mx-auto">
        <div class="min-h-screen flex flex-col">
            <div class="flex-1 flex flex-row">
                <div class="w-full">
                    <livewire:frontend.post-cards />

                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <livewire:frontend.footer />
@endsection

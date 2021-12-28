<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">

    <div class="col-md-12 mb-6">
                    <img src="{{ url('images/logo.png') }}" class="rounded mx-auto d-block" width="700" alt="">
                </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                <x-jet-welcome />
            </div>
        </div>
    </div>
</x-app-layout>

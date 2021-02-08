<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(session()->has('error'))
                    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-500">
                        <span class="inline-block align-middle mr-8">
                            <b class="capitalize">Error!</b> {{ session()->get('error') }}
                        </span>
                    </div>
                @endif

                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

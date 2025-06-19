<x-app-layout>
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Achievements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- FLEX ROW CONTAINER --}}
            <div class="flex flex-col md:flex-row gap-6">
                {{-- User's Achievements --}}
                <div class="w-full md:w-1/2 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Your Achievements</h3>
                    <ul class="list-none space-y-2 text-gray-700">
                        @foreach ($userAchievements as $achievement)
                            <li class="flex items-center space-x-2">
                                <i class="fas {{ $achievement->icon }}"></i>
                                <span>{{ $achievement->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                {{-- Locked Achievements --}}
                <div class="w-full md:w-1/2 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Locked Achievements</h3>
                    <ul class="list-none space-y-2 text-gray-700">
                        @foreach ($LockedAchievements as $achievement)
                            <li class="flex items-center space-x-2">
                                <i class="fas {{ $achievement->icon }}"></i>
                                <span>{{ $achievement->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

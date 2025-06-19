<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leaderboard') }}
            
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Top 10 Leaderboard</h1>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topUsers as $index => $player)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $player->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $player->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">{{ $player->rank }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

                @if($topUsers->isEmpty())
                    <p class="mt-4 text-gray-500">No players found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

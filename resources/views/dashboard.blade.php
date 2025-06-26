{{-- resources/views/dashboard.blade.php --}}

@vite(['resources/js/app.js', 'resources/js/functions.js', 'resources/js/notifications.js'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Inject PHP Data into JS --}}
    <script>
        window.lastAchievement = @json($lastAchievement ?? null);
        const gameOverFromServer = {{ $gameOver ?? 0 }};
        const boardFromServer = {!! json_encode($data ?? array_fill(0, 9, "")) !!};
        const playerFromServer = {!! json_encode($lastplayer ?? "X") !!};
        const playerNameFromServer = @json(auth()->check() ? auth()->user()->name : 'Guest');
        const game_idFromServer = {{ $game_id ?? 0 }};
    </script>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12" data-user-id="{{ auth()->check() ? auth()->id() : '' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h1 class="text-center text-2xl font-bold mb-4">Tic-Tac-Toe</h1>

                {{-- Desktop Game Settings --}}
                <div class="text-center mb-4 hidden sm:block">
                    <label for="starter">Who starts first?</label>
                    <select id="starter" class="ml-2 p-1 w-36 border rounded">
                        <option value="human" selected>You</option>
                        <option value="ai">AI</option>
                    </select>

                    @if (!$gameOver)
                        <div id="continue-wrapper" class="text-center mt-4">
                            <button id="continueGame" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Continue</button>
                        </div>
                    @endif

                    <label for="difficulty" class="ml-4">Difficulty:</label>
                    <select id="difficulty" class="ml-2 p-1 w-36 border rounded">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>

                    <button id="startGame" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Start Game</button>
                </div>
                {{-- Mobile Game Settings Modal --}}
                <div x-data="{ showGameSettings: false }" x-cloak>
                        @if (!$gameOver)
                            <div id="continue-wrapper-mobile" class="text-center mt-4 sm:hidden">
                                <button id="continueGameMobile" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full">
                                    Continue
                                </button>
                            </div>
                        @endif
                    <!-- Mobile Button to Open Modal -->
                    <div class=" inset-x-0 flex justify-center sm:hidden z-50 pointer-events-none">
                        <div class="pointer-events-auto">
                            <button
                                @click="showGameSettings = true"
                                class="bg-blue-600 text-white px-5 py-2 rounded-full shadow-lg hover:bg-blue-700 mb-4"
                            >
                                Game Settings
                            </button>
                        </div>
                    </div>
                    <!-- Modal Background -->
                    <div x-show="showGameSettings" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <!-- Modal Content -->
                        <div class="bg-white rounded-lg p-6 w-11/12 max-w-sm">
                            <h2 class="text-lg font-semibold mb-4 text-center">Game Settings</h2>

                            <!-- Starter -->
                            <div class="mb-4">
                                <label for="mobile-starter" class="block font-medium mb-1">Who starts first?</label>
                                <select id="mobile-starter" class="w-full p-2 border rounded">
                                    <option value="human" selected>You</option>
                                    <option value="ai">AI</option>
                                </select>
                            </div>

                            <!-- Difficulty -->
                            <div class="mb-4">
                                <label for="mobile-difficulty" class="block font-medium mb-1">Difficulty:</label>
                                <select id="mobile-difficulty" class="w-full p-2 border rounded">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>

                            <div class="flex justify-between">
                                <button @click="showGameSettings = false"
                                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>

                                <button id="applySettingsMobile" @click="showGameSettings = false"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Start</button>
                            </div>
                        </div>
                    </div>
                </div>
                

                {{-- Game Board --}}
                <div class="relative w-72 h-72 mx-auto grid grid-cols-3 grid-rows-3">
                    @for ($i = 0; $i < 9; $i++)
                        <button
                            class="cell border border-transparent text-3xl font-bold bg-white flex items-center justify-center"
                            style="width: 6rem; height: 6rem;"
                            data-index="{{ $i }}">
                        </button>
                    @endfor

                    {{-- Grid Lines --}}
                    <div class="absolute top-0 bottom-0 left-1/3 w-1 bg-black z-10"></div>
                    <div class="absolute top-0 bottom-0 left-2/3 w-1 bg-black z-10"></div>
                    <div class="absolute left-0 right-0 top-1/3 h-1 bg-black z-10"></div>
                    <div class="absolute left-0 right-0 top-2/3 h-1 bg-black z-10"></div>
                </div>

                <div class="text-center mt-4">
                    <button id="reset" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reset Game</button>
                </div>

                <div class="flex justify-between mt-4 text-gray-700">
                    <div id="status" class="text-left font-medium"></div>
                    <div id="wins" class="text-right font-medium"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<style>
    [x-cloak] { display: none !important; }
</style>

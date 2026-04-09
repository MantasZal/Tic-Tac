

<?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', 'resources/js/functions.js', 'resources/js/notifications.js']); ?>

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    
    <script>
        window.lastAchievement = <?php echo json_encode($lastAchievement ?? null, 15, 512) ?>;
        const gameOverFromServer = <?php echo e($gameOver ?? 0); ?>;
        const boardFromServer = <?php echo json_encode($data ?? array_fill(0, 9, "")); ?>;
        const playerFromServer = <?php echo json_encode($lastplayer ?? "X"); ?>;
        const playerNameFromServer = <?php echo json_encode(auth()->check() ? auth()->user()->name : 'Guest', 15, 512) ?>;
        const game_idFromServer = <?php echo e($game_id ?? 0); ?>;
    </script>

    
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <div class="py-12" data-user-id="<?php echo e(auth()->check() ? auth()->id() : ''); ?>">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h1 class="text-center text-2xl font-bold mb-4">Tic-Tac-Toe</h1>

                
                <div class="text-center mb-4 hidden sm:block">
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <div class="flex items-center gap-2">
                            <label for="starter">Who starts first?</label>
                            <select id="starter" class="p-1 w-36 border rounded">
                                <option value="human" selected>You</option>
                                <option value="ai">AI</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="opponent">Mode:</label>
                            <select id="opponent" class="p-1 w-36 border rounded">
                                <option value="ai" selected>Vs AI</option>
                                <option value="two_player">Two Players</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="difficulty">Difficulty:</label>
                            <select id="difficulty" class="p-1 w-36 border rounded">
                                <option value="easy">Easy</option>
                                <option value="medium" selected>Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>

                        <div>
                            <button id="startGame" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Start Game</button>
                        </div>
                    </div>

                    <?php if(!$gameOver): ?>
                        <div id="continue-wrapper" class="text-center mt-4">
                            <button id="continueGame" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Continue</button>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div x-data="{ showGameSettings: false }" x-cloak>
                        <?php if(!$gameOver): ?>
                            <div id="continue-wrapper-mobile" class="text-center mt-4 sm:hidden">
                                <button id="continueGameMobile" class="px-5 py-2 bg-green-600 text-white rounded-full shadow-lg hover:bg-green-700">
                                    Continue
                                </button>
                            </div>
                        <?php endif; ?>
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

                            <div class="mb-4">
                                <label for="mobile-opponent" class="block font-medium mb-1">Mode:</label>
                                <select id="mobile-opponent" class="w-full p-2 border rounded">
                                    <option value="ai" selected>Vs AI</option>
                                    <option value="two_player">Two Players</option>
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
                

                
                <div class="relative w-72 h-72 mx-auto grid grid-cols-3 grid-rows-3">
                    <?php for($i = 0; $i < 9; $i++): ?>
                        <button
                            class="cell border border-transparent text-3xl font-bold bg-white flex items-center justify-center"
                            style="width: 6rem; height: 6rem;"
                            data-index="<?php echo e($i); ?>">
                        </button>
                    <?php endfor; ?>

                    
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>


<style>
    [x-cloak] { display: none !important; }
</style>
<?php /**PATH C:\Users\manta\Downloads\Tic-Tac\resources\views/dashboard.blade.php ENDPATH**/ ?>
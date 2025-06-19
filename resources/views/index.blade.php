<!DOCTYPE html>


<script>
    const gameOverFromServer = {{ $gameOver ?? 0}}; // Outputs 0 or 1
    const boardFromServer = {!! json_encode($data?? array_fill(0, 9, "")) !!}; // Outputs JS array
    const playerFromServer = {!! json_encode($lastplayer ?? "X") !!};
</script>
<body data-user-id="{{ auth()->check() ? auth()->id() : '' }}">

</script> 

<head>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/js/app.js'])
 
</head>
<body>
  <h1 class="text-center">Kryziukai nuliuka </h1>
  <div class="text-center my-4">
  <label for="starter">Who starts first?</label>
  <select id="starter" class="ml-2 p-1 border">
  <option value="human" selected>Human</option>
  <option value="ai">AI</option>
  </select>
</div>
<select id="difficulty">
  <option value="easy">Easy</option>
  <option value="medium" selected>Medium</option>
  <option value="hard">Hard</option>
</select>

<button id="startGame">Start game</button>



<div class="grid grid-cols-3 gap-3 text-center p-4 bg-black">
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
  <button class="p-4 bg-white "></button>
</div>

<button id="reset">Reset Game</button>
<div style="display: flex; justify-content: space-between;">
  
  <div id="status" style="text-align: left; flex: 1;"></div>
  <div id="wins" style="text-align: right; flex: 1;"></div>
</div>


</body>
</html>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Importante para las solicitudes POST en Laravel -->
    <link href="{{ asset('css/game.css') }}" rel="stylesheet">
    <title>Tic Tac Toe Game</title>
</head>
<body>
    <div class="contenedor">
        <div class="board">
            @for ($row = 0; $row < 3; $row++)
                @for ($col = 0; $col < 3; $col++)
                    <div class="cell" data-row="{{ $row }}" data-col="{{ $col }}" onclick="makeMove({{ $row }}, {{ $col }})"></div>
                @endfor
            @endfor
        </div>
        <button class="start-button" onclick="startGame()">Start</button>
    </div>
    <script src="{{ asset('js/game.js') }}"></script>
</body>
</html>

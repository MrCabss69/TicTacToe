<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class GameController extends Controller
{
    public function show()
    {
        return view('game');
    }

    public function start()
    {
        $gameId = uniqid('game_', true);
        $games = session('games', []);
        $games[$gameId] = ['board' => array_fill(0, 3, array_fill(0, 3, null)), 'currentPlayer' => 'X', 'status' => 'active', 'winner' => null];
        session(['games' => $games]);
        Log::info("Game started. Game ID: $gameId");
        return response()->json(['success' => true, 'gameId' => $gameId, 'currentPlayer' => 'X']);
    }

    public function move(Request $request)
    {
        $validated = $request->validate([
            'gameId' => 'required',
            'row' => 'required|integer|between:0,2',
            'col' => 'required|integer|between:0,2',
        ]);

        $gameId = $validated['gameId'];
        $games = session('games', []);
        $gameState = $games[$gameId] ?? null;

        if (!$gameState || $gameState['status'] === 'finished') {
            return response()->json(['success' => false, 'message' => 'Partida no válida o ya finalizada.']);
        }

        $board = &$gameState['board'];
        if ($board[$validated['row']][$validated['col']] !== null) {
            return response()->json(['success' => false, 'message' => 'Casilla ya ocupada.']);
        }

        // Realiza el movimiento del jugador
        $board[$validated['row']][$validated['col']] = $gameState['currentPlayer'];

        // Verifica si hay un ganador después del movimiento del jugador
        $winner = $this->checkWinner($board);
        if ($winner) {
            $gameState['status'] = 'finished';
            $gameState['winner'] = $winner;
        } else {
            $gameState = $this->requestBotMove($gameState, $gameId);
        }

        $games[$gameId] = $gameState;
        session(['games' => $games]);

        return response()->json(['success' => true, 'state' => $gameState]);
    }

    public function requestBotMove($gameState, $gameId)
    {
        try {
            $botResponse = Http::post('http://localhost:5000/move', ['board' => $gameState['board']]);
            if ($botResponse->successful()) {
                $botMove = $botResponse->json();
                
                // Comprobar si el bot ha devuelto un movimiento válido
                $row = isset($botMove['row']) ? (int) $botMove['row'] : null;
                $col = isset($botMove['col']) ? (int) $botMove['col'] : null;

                // Verificar que row y col no son null y que la casilla está vacía
                if ($row !== null && $col !== null && $gameState['board'][$row][$col] === null) {
                    $gameState['board'][$row][$col] = 'O'; // Suponiendo que el bot juega como 'O'
                    $winner = $this->checkWinner($gameState['board']); // Asumiendo que tienes esta función implementada
                    
                    if ($winner !== null) { // Asumiendo que checkWinner retorna null si no hay ganador aún
                        $gameState['status'] = 'finished';
                        $gameState['winner'] = $winner; // Asumiendo que checkWinner retorna 'X', 'O' o 'draw'
                    } else if ($this->isBoardFull($gameState['board'])) { // Asumiendo que tienes esta función implementada
                        // No hay ganador y el tablero está lleno, es un empate
                        $gameState['status'] = 'finished';
                        $gameState['winner'] = 'draw';
                    }
                } else {
                    // Manejo del caso en que no haya movimientos válidos o el tablero esté lleno
                    // Puedes decidir cómo manejar este caso, por ejemplo, marcándolo como empate o lanzando un error
                }
            } else {
                throw new Exception('Error al solicitar movimiento al bot.');
            }
        } catch (Exception $e) {
            Log::error('Error in requestBotMove: ' . $e->getMessage());
        }
        return $gameState;
}

    // Función adicional para comprobar si el tablero está lleno
    protected function isBoardFull($board)
    {
        foreach ($board as $row) {
            if (in_array(null, $row, true)) {
                return false;
            }
        }
        return true;
    }

    private function checkWinner($board)
    {
        $lines = [
            [[0, 0], [0, 1], [0, 2]],
            [[1, 0], [1, 1], [1, 2]],
            [[2, 0], [2, 1], [2, 2]],
            [[0, 0], [1, 0], [2, 0]],
            [[0, 1], [1, 1], [2, 1]],
            [[0, 2], [1, 2], [2, 2]],
            [[0, 0], [1, 1], [2, 2]],
            [[0, 2], [1, 1], [2, 0]],
        ];

        foreach ($lines as $line) {
            [$a, $b, $c] = $line;
            if ($board[$a[0]][$a[1]] && $board[$a[0]][$a[1]] === $board[$b[0]][$b[1]] && $board[$a[0]][$a[1]] === $board[$c[0]][$c[1]]) {
                return $board[$a[0]][$a[1]];
            }
        }

        return null;
    }
}

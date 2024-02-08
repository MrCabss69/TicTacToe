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
            session([$gameId => ['board' => array_fill(0, 3, array_fill(0, 3, null)), 'currentPlayer' => 'X', 'status' => 'active']]);
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
            $gameState = session()->get($gameId);

            if (!$gameState || $gameState['status'] === 'finished') {
                return response()->json(['success' => false, 'message' => 'Partida no válida o ya finalizada.'], 404);
            }

            $board = &$gameState['board'];
            if ($board[$validated['row']][$validated['col']] !== null) {
                return response()->json(['success' => false, 'message' => 'Casilla ya ocupada.']);
            }

            $board[$validated['row']][$validated['col']] = $gameState['currentPlayer'];
            $winner = $this->checkWinner($board);

            if ($winner) {
                $gameState['status'] = 'finished';
                $gameState['winner'] = $winner;
            } else {
                // Solo solicitar movimiento del bot si no hay ganador
                $gameState = $this->requestBotMove($gameState, $gameId);
            }

            session([$gameId => $gameState]);

            return response()->json(['success' => true, 'state' => $gameState]);
        }

        public function requestBotMove($gameState, $gameId)
        {
            try {
                $botResponse = Http::post('http://localhost:5000/move', ['board' => $gameState['board']]);
                if ($botResponse->successful()) {
                    $botMove = $botResponse->json();
                    $board = &$gameState['board'];
                    $row = $botMove['row'];
                    $col = $botMove['col'];

                    if ($board[$row][$col] === null) {
                        $board[$row][$col] = 'O'; // Asume que el bot juega con 'O'

                        $winner = $this->checkWinner($board);
                        if ($winner) {
                            $gameState['status'] = 'finished';
                            $gameState['winner'] = $winner;
                        }
                    }
                } else {
                    throw new Exception('Error al solicitar movimiento al bot.');
                }
            } catch (Exception $e) {
                Log::error('Error in requestBotMove: ' . $e->getMessage());
                // Considera qué hacer en caso de fallo. ¿Reintentar, marcar el juego como finalizado, otro?
            }

            // Cambia el turno de vuelta al jugador humano solo si el juego continúa
            if ($gameState['status'] !== 'finished') {
                $gameState['currentPlayer'] = 'X';
            }

            return $gameState;
        }


        private function checkWinner($board)
        {
            for ($i = 0; $i < 3; $i++) {
                if ($board[$i][0] !== null &&
                    $board[$i][0] === $board[$i][1] &&
                    $board[$i][1] === $board[$i][2]) {
                    return $board[$i][0];
                }

                if ($board[0][$i] !== null &&
                    $board[0][$i] === $board[1][$i] &&
                    $board[1][$i] === $board[2][$i]) {
                    return $board[0][$i];
                }
            }

            if ($board[0][0] !== null &&
                $board[0][0] === $board[1][1] &&
                $board[1][1] === $board[2][2]) {
                return $board[0][0];
            }

            if ($board[0][2] !== null &&
                $board[0][2] === $board[1][1] &&
                $board[1][1] === $board[2][0]) {
                return $board[0][2];
            }

            return null;
        }
    }

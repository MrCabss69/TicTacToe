<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;


    class GameController extends Controller
    {
        private static $board = [];
        private static $currentPlayer = 'X';

        public function __construct()
        {
            if (empty(self::$board)) {
                self::$board = array_fill(0, 3, array_fill(0, 3, null)); // Crea una matriz 3x3 vacía solo si aún no se ha inicializado
            }
        }

        public function show()
        {
            return view('game');
        }

        public function start(Request $request)
        {
            $board = array_fill(0, 3, array_fill(0, 3, null));
            session(['board' => $board]);
            session(['currentPlayer' => 'X']);

            return response()->json([
                'success' => true,
                'currentPlayer' => session('currentPlayer')
            ]);
        }

        public function move(Request $request)
        {
            $row = $request->input('row');
            $col = $request->input('col');
            $player = $request->input('player');
            $board = session('board', array_fill(0, 3, array_fill(0, 3, null)));

            if ($row >= 0 && $row < 3 && $col >= 0 && $col < 3 && $board[$row][$col] === null) {
                $board[$row][$col] = $player; // Actualizar el tablero
                session(['board' => $board]); // Guardar el nuevo estado del tablero en la sesión

                // Verificar si hay un ganador después del movimiento
                $winner = $this->checkWinner($board);

                // Cambiar el turno del jugador y guardar en la sesión
                $currentPlayer = (session('currentPlayer') === 'X') ? 'O' : 'X';
                session(['currentPlayer' => $currentPlayer]);

                return response()->json([
                    'success' => true,
                    'board' => $board,
                    'winner' => $winner, // Puede ser null si no hay ganador aún
                    'currentPlayer' => $currentPlayer
                ]);
            } else {
                // Si el movimiento no es válido o la casilla ya está ocupada
                return response()->json(['success' => false, 'message' => 'Movimiento no válido o casilla ya ocupada.']);
            }
        }

        private function checkWinner($board)
        {
            // Verificar filas y columnas
            for ($i = 0; $i < 3; $i++) {
                // Verificar fila
                if ($board[$i][0] !== null &&
                    $board[$i][0] === $board[$i][1] &&
                    $board[$i][1] === $board[$i][2]) {
                    return $board[$i][0];
                }

                // Verificar columna
                if ($board[0][$i] !== null &&
                    $board[0][$i] === $board[1][$i] &&
                    $board[1][$i] === $board[2][$i]) {
                    return $board[0][$i];
                }
            }

            // Verificar diagonales
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
            return null; // No hay ganador aún
        }
    }

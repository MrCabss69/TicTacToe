let currentPlayer = 'X'; // Inicializa el primer jugador como 'X'
let gameOver = false; // El juego no ha terminado al inicio
let gameId; // Almacena el ID de la partida actual

const startGame = () => {
    fetch('/api/game/start', {  // Asegúrate de que esta ruta coincida con la definida en tu Laravel
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            //'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            gameId = data.gameId;
            document.querySelectorAll('.cell').forEach(cell => {
                cell.textContent = '';
                cell.classList.remove('disabled'); // Asegúrate de que las celdas sean clickeables después de iniciar un nuevo juego
            });
            gameOver = false;
            currentPlayer = data.currentPlayer;
        } else {
            console.error('Error al iniciar el juego:', data.message);
        }
    })
    .catch(error => console.error('Error al iniciar el juego:', error));
};


function makeMove(row, col) {
    if (gameOver || document.querySelector(`[data-row="${row}"][data-col="${col}"]`).classList.contains('disabled')) {
        alert('Movimiento no permitido. Por favor, intenta de nuevo.');
        return; // Salir si el juego ya terminó o la celda está ocupada
    }

    fetch('/api/game/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ gameId, row, col, player: currentPlayer })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response from move API:', data);
        if (data.success) {
            updateBoard(data.state.board);
            // Actualizar el jugador actual basado en la respuesta del servidor
            currentPlayer = data.state.currentPlayer;
            
            if (data.state.winner) {
                alert(`Ganador: ${data.state.winner}`);
                gameOver = true; // Finaliza el juego si hay un ganador
            }
        } else {
            console.error('Movimiento no permitido o error al procesar el movimiento');
        }
    })
    .catch(error => console.error('Error:', error));
}



function updateBoard(board) {
    for (let row = 0; row < board.length; row++) {
        for (let col = 0; col < board[row].length; col++) {
            const cell = document.querySelector(`[data-row="${row}"][data-col="${col}"]`);
            cell.textContent = board[row][col];
            if (board[row][col] !== null) {
                cell.classList.add('disabled'); // Marca la celda como ocupada
            }
        }
    }
}

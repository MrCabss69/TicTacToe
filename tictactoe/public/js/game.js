let currentPlayer = 'X'; // Inicializa el primer jugador como 'X'
let gameOver = true; // Añade una variable para rastrear el estado del juego

function startGame() {
    fetch('/game/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Limpia el tablero en la interfaz de usuario
        document.querySelectorAll('.cell').forEach(cell => {
            cell.textContent = '';
        });

        gameOver = false; // Reinicia el estado del juego

        if (data.currentPlayer) {
            currentPlayer = data.currentPlayer; // Establece el jugador inicial según el servidor
        }
    })
    .catch(error => {
        console.error('Error al iniciar el juego:', error);
        alert('Hubo un error al iniciar el juego. Por favor, inténtalo de nuevo.');
    });
}

function makeMove(row, col) {
    if (gameOver) {
        alert('El juego ha terminado. Por favor, inicia un nuevo juego.');
        return; // Salir si el juego ya terminó
    }

    fetch('/game/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ row, col, player: currentPlayer })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cell = document.querySelector(`[data-row="${row}"][data-col="${col}"]`);
            cell.textContent = currentPlayer; // Actualiza la celda con el movimiento realizado

            if (data.winner) {
                alert(`Winner: ${data.winner}`);
                gameOver = true;
            }

            currentPlayer = currentPlayer === 'X' ? 'O' : 'X'; // Cambia el jugador
        } else {
            console.error('Move not allowed or error processing the move');
        }
    })
    .catch(error => console.error('Error:', error));
}

# Tic Tac Toe

This project is a web implementation of the classic Tic Tac Toe game, allowing players to interact with a 3x3 board and play against another player on the same device. The backend is built with Laravel, taking advantage of its capabilities to handle game state, while the frontend uses Angular to interact with the user and communicate with the server.

## Characteristics

- Tic Tac Toe game for two players on the same device.
- Restart of the game and determination of the winner on the server.
- Interactive user interface that reflects the current state of the game.

## Used technology

- Backend: Laravel (Specified Version)
- Frontend: HTML, CSS, pure JavaScript
- State Management: Laravel Session

## Project Structure
```bash
/tictactoe
    /app
        /Http
            /Controllers
                GameController.php
    /public
        /css
            game.css
        /js
            game.js
/resources
    /views
        game.blade.php
/routes
    web.php
```

## Configuration and Installation

### 1. **Clone the Repository**

```bash
git clone repository-url
```

### 2. **Install Composer Dependencies**

From the project directory, run:


```bash
composer install
```

### 3. **Run Development Server**

```bash
php artisan serve
```

The game should now be accessible at http://localhost:8000/game.


### 4. **Use**

- Start a Game: Access the /game path in your browser. Click the "Start" button to start a new game.

- Make a Move: Click on any empty cell to place your symbol (X or O).

- Win the Game: The first player to align three of their symbols vertically, horizontally or diagonally wins. The game will indicate the winner with a pop-up message.

- Restart Game: After finishing a game, click "Start" again to restart.
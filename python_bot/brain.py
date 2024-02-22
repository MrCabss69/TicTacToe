def is_terminal(board):
    """ Check if the game has ended. """
    # Check for a win
    for row in board:
        if row.count(row[0]) == 3 and row[0] != '':
            return True
    for col in range(3):
        if board[0][col] == board[1][col] == board[2][col] != '':
            return True
    if board[0][0] == board[1][1] == board[2][2] != '' or board[0][2] == board[1][1] == board[2][0] != '':
        return True
    # Check for a draw
    if all(cell != '' for row in board for cell in row):
        return True
    return False

def get_winner(board):
    """ Determine the game's winner, if any. """
    def check_win(player):
        lines = [
            board[0], board[1], board[2],
            [board[0][0], board[1][0], board[2][0]], [board[0][1], board[1][1], board[2][1]], [board[0][2], board[1][2], board[2][2]],
            [board[0][0], board[1][1], board[2][2]], [board[0][2], board[1][1], board[2][0]]
        ]
        return any(all(cell == player for cell in line) for line in lines)
    if check_win('X'):
        return 1
    elif check_win('O'):
        return -1
    return 0

def get_current_piece(board):
    """ Determine whose turn it is. """
    x_count = sum(row.count('X') for row in board)
    o_count = sum(row.count('O') for row in board)
    return 'X' if x_count == o_count else 'O'

def get_all_moves(board):
    """ Generate a list of all possible moves. """
    return [(row, col) for row in range(3) for col in range(3) if board[row][col] == '']

def apply_move(board, move):
    """ Apply a move to the board. """
    new_board = [row[:] for row in board]  # Make a deep copy of the board
    piece = get_current_piece(new_board)
    new_board[move[0]][move[1]] = piece
    return new_board

def eval_board(board):
    """ Evaluate the board for non-terminal states. """
    if is_terminal(board):
        return get_winner(board)
    
    def evaluate_line(line):
        if line.count('X') > 0 and line.count('O') == 0:
            # Línea potencial para 'X'
            return line.count('X')
        elif line.count('O') > 0 and line.count('X') == 0:
            # Línea potencial para 'O'
            return -line.count('O')
        return 0  # Línea bloqueada o mixta

    score = 0
    # Evaluar filas
    for row in board:
        score += evaluate_line(row)
    # Evaluar columnas
    for col in range(3):
        score += evaluate_line([board[row][col] for row in range(3)])
    # Evaluar diagonales
    score += evaluate_line([board[i][i] for i in range(3)])
    score += evaluate_line([board[i][2-i] for i in range(3)])

    return score

    

def get_best_move(board: list, alfa: int = -1000000, beta: int = 1000000, depth: int = 3) -> tuple:
    if depth == 0 or is_terminal(board):
        return board, eval_board(board) 
    best_move = None
    current_piece = get_current_piece(board)
    is_maximizing = current_piece == 'X'
    best_eval = float('-inf') if is_maximizing else float('inf')

    for move in get_all_moves(board):
        board_copy = apply_move(board, move)
        _, move_eval = get_best_move(board_copy, alfa, beta, depth-1) 
        if is_maximizing:
            if move_eval > best_eval:
                best_eval, best_move = move_eval, move
                alfa = max(alfa, best_eval)
        else:
            if move_eval < best_eval:
                best_eval, best_move = move_eval, move
                beta = min(beta, best_eval)

        if beta <= alfa:
            break  # pruning
    return best_move, best_eval
                
def choose_move(board):
    # Esta función debería usar get_best_move para elegir el mejor movimiento
    best_move,_ = get_best_move(board, alfa=-1000000, beta=1000000, depth=3)
    return best_move
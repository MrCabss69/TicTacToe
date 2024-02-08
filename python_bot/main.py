from flask import Flask, request, jsonify
import random

app = Flask(__name__)

@app.route('/move', methods=['POST'])
def bot_move():
    data = request.json
    board = data['board']
    empty_cells = [(row, col) for row in range(3) for col in range(3) if board[row][col] is None]
    move =  random.choice(empty_cells)if empty_cells else None
    move = {'row': move[0], 'col': move[1]}
    return jsonify(move)

if __name__ == '__main__':
    app.run(debug=True, port=5000)

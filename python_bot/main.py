from flask import Flask, request, jsonify
from brain import choose_move

app = Flask(__name__)


@app.route('/move', methods=['POST'])
def bot_move():
    data = request.json
    board = data['board']
    board = [[cell if cell is not None else '' for cell in row] for row in board]
    best_move = choose_move(board)
    if best_move:
        move = {'row': best_move[0], 'col': best_move[1]}
    else:
        move = {'row': -1, 'col': -1} 
    return jsonify(move)

if __name__ == '__main__':
    app.run(debug=True, port=5000)

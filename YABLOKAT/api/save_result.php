<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit();
}
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
$playerName = trim($data['playerName'] ?? '');
$opponentName = trim($data['opponentName'] ?? '');
$difficulty = $data['difficulty'] ?? '';
$playerScore = intval($data['playerScore'] ?? 0);
$opponentScore = intval($data['opponentScore'] ?? 0);
$roundsPlayed = intval($data['roundsPlayed'] ?? 0);
if (empty($playerName) || empty($opponentName) || empty($difficulty)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Недостаточно данных']);
    exit();
}
$validDifficulties = ['Новичок', 'Профи', 'Хакер', 'Яблокатер'];
if (!in_array($difficulty, $validDifficulties)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверная сложность']);
    exit();
}
$isWinner = $playerScore < $opponentScore;
try {
    $pdo = getDbConnection();
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        INSERT INTO game_results 
        (user_id, player_name, opponent_name, difficulty, player_score, opponent_score, is_winner, rounds_played) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId, 
        $playerName, 
        $opponentName, 
        $difficulty, 
        $playerScore, 
        $opponentScore, 
        $isWinner, 
        $roundsPlayed
    ]);
    $stmt = $pdo->prepare("
        UPDATE user_statistics 
        SET total_games = total_games + 1,
            total_wins = total_wins + ?,
            total_losses = total_losses + ?,
            best_score = LEAST(best_score, ?)
        WHERE user_id = ?
    ");
    $stmt->execute([
        $isWinner ? 1 : 0,
        $isWinner ? 0 : 1,
        $playerScore,
        $userId
    ]);
    echo json_encode([
        'success' => true,
        'message' => 'Результат сохранен'
    ]);
} catch (PDOException $e) {
    error_log("Save result error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера. Попробуйте позже']);
}
?>
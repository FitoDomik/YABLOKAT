<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit();
}
try {
    $pdo = getDbConnection();
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT total_games, total_wins, total_losses, best_score 
        FROM user_statistics 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();
    $stmt = $pdo->prepare("
        SELECT player_name, opponent_name, difficulty, player_score, opponent_score, 
               is_winner, rounds_played, played_at 
        FROM game_results 
        WHERE user_id = ? 
        ORDER BY played_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$userId]);
    $recentGames = $stmt->fetchAll();
    echo json_encode([
        'success' => true,
        'stats' => $stats ?: [
            'total_games' => 0,
            'total_wins' => 0,
            'total_losses' => 0,
            'best_score' => 999
        ],
        'recentGames' => $recentGames
    ]);
} catch (PDOException $e) {
    error_log("Get stats error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
}
?>
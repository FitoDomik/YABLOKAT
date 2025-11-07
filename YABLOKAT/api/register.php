<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit();
}
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Недействительный CSRF токен']);
    exit();
}
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$errors = [];
if (empty($username)) {
    $errors[] = 'Введите никнейм';
} elseif (strlen($username) < 3) {
    $errors[] = 'Никнейм должен содержать минимум 3 символа';
} elseif (strlen($username) > 50) {
    $errors[] = 'Никнейм не должен превышать 50 символов';
} elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ0-9_-]+$/u', $username)) {
    $errors[] = 'Никнейм может содержать только буквы, цифры, дефис и подчеркивание';
}
if (empty($password)) {
    $errors[] = 'Введите пароль';
} elseif (strlen($password) < 6) {
    $errors[] = 'Пароль должен содержать минимум 6 символов';
}
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit();
}
try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким никнеймом уже существует']);
        exit();
    }
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->execute([$username, $passwordHash]);
    $userId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("INSERT INTO user_statistics (user_id) VALUES (?)");
    $stmt->execute([$userId]);
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    echo json_encode([
        'success' => true,
        'message' => 'Регистрация успешна',
        'user' => [
            'id' => $userId,
            'username' => $username
        ]
    ]);
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера. Попробуйте позже']);
}
?>
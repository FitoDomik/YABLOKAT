<?php
require_once __DIR__ . '/config/config.php';
if (isLoggedIn()) {
    header('Location: /');
    exit();
}
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | Яблокат</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="theme-toggle">
        <button id="themeToggle" aria-label="Переключить тему">
            <span class="theme-icon">🌙</span>
        </button>
    </div>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Вход</h1>
            <form id="loginForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <div class="form-group">
                    <label for="username">Никнейм</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div id="errorMessage" class="error-message"></div>
                <div class="form-submit">
                    <button type="submit" class="btn btn-primary">Войти</button>
                </div>
            </form>
            <div class="auth-links">
                <a href="/register.php">Нет аккаунта? Зарегистрироваться</a>
                <a href="/">Вернуться на главную</a>
            </div>
        </div>
    </div>
    <script src="/js/theme.js"></script>
    <script src="/js/auth.js"></script>
</body>
</html>
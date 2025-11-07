<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u3307679_YABLOKAT');
define('DB_USER', 'u3307679_YABLOKAT');
define('DB_PASS', 'u3307679_YABLOKAT');
define('DB_CHARSET', 'utf8mb4');
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        header("Location: /errors/500.html");
        exit();
    }
}
?>
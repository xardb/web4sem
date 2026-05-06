<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO(
        'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
        'webuser',
        'webpass',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$db->exec("
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        login VARCHAR(50) NOT NULL UNIQUE,
        pass_hash VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$login = 'admin';
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    INSERT INTO admin (login, pass_hash)
    VALUES (:login, :pass_hash)
    ON DUPLICATE KEY UPDATE pass_hash = VALUES(pass_hash)
");

$stmt->execute([
    ':login' => $login,
    ':pass_hash' => $hash
]);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ создан</title>
    <link rel="stylesheet" href="../task05/styles.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Админ создан</h1>
    </div>
</header>

<main>
    <div class="container">
        <section>
            <h2>Готово</h2>

            <p>Администратор создан или обновлён.</p>

            <p>
                <strong>Логин:</strong> admin<br>
                <strong>Пароль:</strong> 123
            </p>

            <p>
                <a href="admin.php">Перейти в админку</a> |
                <a href="../murlyka-shelter-main/index.php">На сайт</a>
            </p>
        </section>
    </div>
</main>

</body>
</html>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$error = '';

/* ===== ПОДКЛЮЧЕНИЕ К БД ===== */

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
    die('Ошибка подключения к базе данных.');
}

/* ===== ЕСЛИ УЖЕ АВТОРИЗОВАН ===== */

if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php?id=' . urlencode($_SESSION['user_id']));
    exit();
}

/* ===== ОБРАБОТКА ВХОДА ===== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } else {
        $stmt = $db->prepare("
            SELECT id, login, pass_hash
            FROM application
            WHERE login = :login
            LIMIT 1
        ");

        $stmt->execute([
            ':login' => $login
        ]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['pass_hash'])) {
            $error = 'Неверный логин или пароль.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];

            setcookie('login', $user['login'], time() + 3600 * 24 * 30, '/');
            setcookie('save', '1', time() + 3600 * 24 * 30, '/');

            header('Location: profile.php?id=' . urlencode($user['id']));
            exit();
        }
    }
}

/* ===== АВТОПОДСТАНОВКА ЛОГИНА ИЗ COOKIE ===== */

$savedLogin = $_COOKIE['login'] ?? '';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в профиль</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Вход в профиль</h1>
    </div>
</header>

<main>
    <div class="container">
        <section>
            <h2>Авторизация</h2>

            <?php if ($error !== ''): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input
                        type="text"
                        id="login"
                        name="login"
                        value="<?= htmlspecialchars($savedLogin, ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <button type="submit">Войти</button>
            </form>

            <p style="margin-top: 20px;">
                <a href="../murlyka-shelter-main/index.php">Вернуться на главную</a>
            </p>
        </section>
    </div>
</main>

</body>
</html>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$error = '';

try {
    $db = new PDO(
        'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

/* Если уже вошёл как админ */
if (!empty($_SESSION['admin_id'])) {
    header('Location: ../task06/admin.php');
    exit();
}

/* Если уже вошёл как обычный пользователь */
if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php?id=' . urlencode($_SESSION['user_id']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } else {
        /* 1. Сначала проверяем администратора */
        $stmt = $db->prepare("
            SELECT id, login, pass_hash
            FROM admin
            WHERE login = :login
            LIMIT 1
        ");

        $stmt->execute([
            ':login' => $login
        ]);

        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['pass_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_login'] = $admin['login'];

            header('Location: ../task06/admin.php');
            exit();
        }

        /* 2. Если это не админ — проверяем обычного пользователя */
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

        if ($user && password_verify($password, $user['pass_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];

            setcookie('login', $user['login'], time() + 3600 * 24 * 30, '/');
            setcookie('save', '1', time() + 3600 * 24 * 30, '/');

            header('Location: profile.php?id=' . urlencode($user['id']));
            exit();
        }

        $error = 'Неверный логин или пароль.';
    }
}

$savedLogin = $_COOKIE['login'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Вход</h1>
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
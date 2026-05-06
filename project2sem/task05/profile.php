<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

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

/* ===== ПРОВЕРКА АВТОРИЗАЦИИ ===== */

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];
$error = '';
$success = '';

/* ===== ОБНОВЛЕНИЕ ДАННЫХ ===== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio   = trim($_POST['bio'] ?? '');

    if ($name === '') {
        $error = 'Заполните имя.';
    } elseif ($phone === '') {
        $error = 'Заполните телефон.';
    } elseif ($email === '') {
        $error = 'Заполните email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email.';
    } else {
        $stmt = $db->prepare("
            UPDATE application
            SET name = :name,
                phone = :phone,
                email = :email,
                bio = :bio
            WHERE id = :id
        ");

        $stmt->execute([
            ':name'  => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':bio'   => $bio,
            ':id'    => $userId
        ]);

        $success = 'Данные успешно обновлены.';
    }
}

/* ===== ПОЛУЧЕНИЕ ДАННЫХ ПОЛЬЗОВАТЕЛЯ ===== */

$stmt = $db->prepare("
    SELECT id, name, phone, email, bio, login, created_at
    FROM application
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ':id' => $userId
]);

$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Профиль пользователя</h1>
    </div>
</header>

<main>
    <div class="container">
        <section>
            <h2>Ваши данные</h2>

            <?php if ($error !== ''): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($success !== ''): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <p>
                <strong>Логин:</strong>
                <?= htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="name">ФИО</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="bio">Сообщение</label>
                    <textarea id="bio" name="bio"><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <button type="submit">Сохранить изменения</button>
            </form>

            <p style="margin-top: 20px;">
                <a href="../murlyka-shelter-main/index.php">На главную</a> |
                <a href="logout.php">Выйти</a>
            </p>
        </section>
    </div>
</main>

</body>
</html>
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

/* ===== СОЗДАНИЕ ТАБЛИЦЫ АДМИНА, ЕСЛИ ЕЁ НЕТ ===== */

$db->exec("
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        login VARCHAR(50) NOT NULL UNIQUE,
        pass_hash VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/*
    Первый запуск:
    если в таблице admin нет записей, создаётся админ:
    login: admin
    password: 123
*/
$countStmt = $db->query("SELECT COUNT(*) AS cnt FROM admin");
$adminCount = (int)$countStmt->fetch()['cnt'];

if ($adminCount === 0) {
    $stmt = $db->prepare("
        INSERT INTO admin (login, pass_hash)
        VALUES (:login, :pass_hash)
    ");

    $stmt->execute([
        ':login' => 'admin',
        ':pass_hash' => password_hash('123', PASSWORD_DEFAULT)
    ]);
}

$error = '';

/* ===== ВЫХОД ИЗ АДМИНКИ ===== */

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_login']);

    header('Location: admin.php');
    exit();
}

/* ===== ОБРАБОТКА ЛОГИНА АДМИНА ===== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login_form'])) {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } else {
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

        if (!$admin || !password_verify($password, $admin['pass_hash'])) {
            $error = 'Неверный логин или пароль.';
        } else {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_login'] = $admin['login'];

            header('Location: admin.php');
            exit();
        }
    }
}

/* ===== ЕСЛИ АДМИН НЕ АВТОРИЗОВАН ===== */

if (empty($_SESSION['admin_id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Вход в админ-панель</title>
        <link rel="stylesheet" href="../task05/styles.css">
    </head>
    <body>

    <header>
        <div class="container">
            <h1>Админ-панель</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <section>
                <h2>Вход администратора</h2>

                <?php if ($error !== ''): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="admin.php">
                    <input type="hidden" name="admin_login_form" value="1">

                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input type="text" id="login" name="login" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit">Войти</button>
                </form>

                <p style="margin-top: 20px;">
                    Первый вход: <strong>admin</strong> / <strong>123</strong>
                </p>

                <p style="margin-top: 20px;">
                    <a href="../murlyka-shelter-main/index.php">Вернуться на сайт</a>
                </p>
            </section>
        </div>
    </main>

    </body>
    </html>
    <?php
    exit();
}

/* ===== ПОЛУЧЕНИЕ ЗАЯВОК ===== */

try {
    $stmt = $db->query("
        SELECT id, name, phone, email, bio, contract, login, created_at
        FROM application
        ORDER BY id DESC
    ");

    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Ошибка получения заявок: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../task05/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .admin-actions {
            margin: 20px 0;
        }

        .admin-table-wrapper {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Админ-панель</h1>
    </div>
</header>

<main>
    <div class="container">
        <section>
            <h2>Заявки с сайта</h2>

            <p>
                Вы вошли как:
                <strong><?= htmlspecialchars($_SESSION['admin_login'], ENT_QUOTES, 'UTF-8') ?></strong>
            </p>

            <div class="admin-actions">
                <a href="../murlyka-shelter-main/index.php">На сайт</a> |
                <a href="admin.php?logout=1">Выйти</a>
            </div>

            <?php if (empty($applications)): ?>
                <p>Заявок пока нет.</p>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Сообщение</th>
                            <th>Согласие</th>
                            <th>Логин</th>
                            <th>Дата</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= (int)$app['id'] ?></td>
                                <td><?= htmlspecialchars($app['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($app['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($app['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= nl2br(htmlspecialchars($app['bio'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
                                <td><?= ((int)$app['contract'] === 1) ? 'Да' : 'Нет' ?></td>
                                <td><?= htmlspecialchars($app['login'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($app['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

</body>
</html>
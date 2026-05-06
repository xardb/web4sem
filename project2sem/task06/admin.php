<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

/* ===== БД ===== */

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

/* ===== CSRF ===== */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* ===== АВТОРИЗАЦИЯ АДМИНА ===== */
/*
   Поддерживает два варианта:
   1. Через обычный вход task05/login.php
   2. Через HTTP Basic, если открыть admin.php напрямую
*/

$isAdminAuthorized = false;

if (!empty($_SESSION['admin_id'])) {
    $isAdminAuthorized = true;
}

if (!$isAdminAuthorized && !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
    $stmt = $db->prepare("SELECT id, login, pass_hash FROM admin WHERE login = ?");
    $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($_SERVER['PHP_AUTH_PW'], $admin['pass_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_login'] = $admin['login'];
        $isAdminAuthorized = true;
    }
}

if (!$isAdminAuthorized) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('<h1>401 Требуется авторизация</h1>');
}

/* ===== ВЫХОД ===== */

if (!empty($_GET['logout'])) {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_login']);

    header('Location: admin.php');
    exit();
}

/* ===== УДАЛЕНИЕ ===== */

if (!empty($_GET['delete'])) {
    if (
        empty($_GET['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])
    ) {
        die('CSRF validation failed');
    }

    $id = (int)$_GET['delete'];

    $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: admin.php');
    exit();
}

/* ===== ОБНОВЛЕНИЕ ===== */

if (!empty($_POST['update'])) {
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die('CSRF validation failed');
    }

    $id = (int)($_POST['id'] ?? 0);

    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $contract = !empty($_POST['contract']) ? 1 : 0;

    if ($id <= 0) {
        die('Некорректный ID пользователя');
    }

    if ($name === '' || $phone === '' || $email === '') {
        die('Имя, телефон и email обязательны');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Некорректный email');
    }

    $stmt = $db->prepare("
        UPDATE application SET
            name = ?,
            phone = ?,
            email = ?,
            bio = ?,
            contract = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $phone,
        $email,
        $bio,
        $contract,
        $id
    ]);

    header('Location: admin.php');
    exit();
}

/* ===== РЕЖИМ РЕДАКТИРОВАНИЯ ===== */

if (!empty($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        die('Пользователь не найден');
    }

    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Редактирование заявки</title>
        <link rel="stylesheet" href="../task05/styles.css">
        <style>
            .admin-wrapper {
                margin-top: 30px;
            }

            .admin-header {
                background: linear-gradient(135deg, #343a40, #495057);
                color: white;
                padding: 40px 0;
                text-align: center;
                margin-bottom: 40px;
            }

            .admin-main-title {
                font-size: 36px;
                margin-bottom: 10px;
                letter-spacing: 1px;
            }

            .admin-subtitle {
                font-size: 16px;
                opacity: 0.85;
            }

            .edit-form {
                max-width: 700px;
                margin-bottom: 30px;
                background: #fff;
                padding: 25px;
                border-radius: 8px;
                border: 1px solid #ddd;
            }

            .edit-form input,
            .edit-form textarea {
                width: 100%;
                margin-bottom: 15px;
                padding: 9px;
            }

            .edit-form textarea {
                min-height: 120px;
            }

            .readonly-field {
                background: #eee;
                color: #555;
            }

            .admin-link {
                display: inline-block;
                margin-top: 15px;
            }
        </style>
    </head>
    <body>

    <header class="admin-header">
        <div class="container">
            <h1 class="admin-main-title">Редактирование заявки</h1>
            <p class="admin-subtitle">Пользователь #<?= (int)$id ?></p>
        </div>
    </header>

    <main>
        <div class="container admin-wrapper">

            <form method="post" class="edit-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int)$id ?>">
                <input type="hidden" name="update" value="1">

                <label>Логин:</label>
                <input
                    class="readonly-field"
                    value="<?= htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8') ?>"
                    readonly
                >

                <label>Имя:</label>
                <input
                    name="name"
                    value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <label>Телефон:</label>
                <input
                    name="phone"
                    value="<?= htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <label>Email:</label>
                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <label>Сообщение:</label>
                <textarea name="bio"><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                <label>
                    <input
                        type="checkbox"
                        name="contract"
                        value="1"
                        <?= ((int)$user['contract'] === 1) ? 'checked' : '' ?>
                        style="width: auto; margin-right: 8px;"
                    >
                    Согласие на обработку данных
                </label>

                <br><br>

                <button type="submit">Сохранить изменения</button>
            </form>

            <a class="admin-link" href="admin.php">Назад в админ-панель</a>

        </div>
    </main>

    <footer>
        <div class="container" style="text-align:center; font-size:14px;">
            © <?= date('Y') ?> Администрирование системы
        </div>
    </footer>

    </body>
    </html>
    <?php
    exit();
}

/* ===== ВЫВОД ПОЛЬЗОВАТЕЛЕЙ ===== */

$users = $db->query("
    SELECT id, name, phone, email, bio, contract, login, created_at
    FROM application
    ORDER BY id DESC
")->fetchAll();

$totalUsers = (int)$db->query("SELECT COUNT(*) FROM application")->fetchColumn();
$totalWithContract = (int)$db->query("SELECT COUNT(*) FROM application WHERE contract = 1")->fetchColumn();
$totalWithoutContract = (int)$db->query("SELECT COUNT(*) FROM application WHERE contract = 0")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Админ-панель</title>
<link rel="stylesheet" href="../task05/styles.css">

<style>
.admin-wrapper {
    margin-top: 30px;
}

.admin-title {
    margin-bottom: 20px;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    background: #fff;
}

.admin-table th,
.admin-table td {
    border: 1px solid #ddd;
    padding: 8px;
    vertical-align: top;
}

.admin-table th {
    background-color: #f2f2f2;
}

.admin-actions a {
    margin-right: 10px;
    text-decoration: none;
    font-weight: bold;
}

.admin-actions a.edit {
    color: #007bff;
}

.admin-actions a.delete {
    color: #dc3545;
}

.admin-header {
    background: linear-gradient(135deg, #343a40, #495057);
    color: white;
    padding: 40px 0;
    text-align: center;
    margin-bottom: 40px;
}

.admin-main-title {
    font-size: 36px;
    margin-bottom: 10px;
    letter-spacing: 1px;
}

.admin-subtitle {
    font-size: 16px;
    opacity: 0.85;
}

.admin-top-actions {
    margin-bottom: 25px;
}

.admin-top-actions a {
    margin-right: 15px;
    font-weight: bold;
    text-decoration: none;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 18px;
    text-align: center;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
}

.table-wrapper {
    overflow-x: auto;
}

.bio-cell {
    max-width: 260px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<header class="admin-header">
  <div class="container">
    <h1 class="admin-main-title">Админ-панель</h1>
    <p class="admin-subtitle">Управление заявками пользователей</p>
  </div>
</header>

<main>
<div class="container admin-wrapper">

    <div class="admin-top-actions">
        <a href="../murlyka-shelter-main/index.php">На сайт</a>
        <a href="admin.php?logout=1">Выйти</a>
    </div>

    <h2 class="admin-title">Статистика</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $totalUsers ?></div>
            <div class="stat-label">Всего заявок</div>
        </div>

        <div class="stat-card">
            <div class="stat-number"><?= $totalWithContract ?></div>
            <div class="stat-label">С согласием</div>
        </div>

        <div class="stat-card">
            <div class="stat-number"><?= $totalWithoutContract ?></div>
            <div class="stat-label">Без согласия</div>
        </div>
    </div>

    <h2 class="admin-title">Заявки</h2>

    <?php if (empty($users)): ?>
        <p>Заявок пока нет.</p>
    <?php else: ?>

    <div class="table-wrapper">
        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Логин</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Сообщение</th>
                <th>Согласие</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= htmlspecialchars($u['login'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="bio-cell"><?= nl2br(htmlspecialchars($u['bio'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
                <td><?= ((int)$u['contract'] === 1) ? 'Да' : 'Нет' ?></td>
                <td><?= htmlspecialchars($u['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="admin-actions">
                    <a class="edit" href="admin.php?edit=<?= (int)$u['id'] ?>">Редактировать</a>
                    <a
                        class="delete"
                        href="admin.php?delete=<?= (int)$u['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                        onclick="return confirm('Удалить эту заявку?')"
                    >
                        Удалить
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php endif; ?>

</div>
</main>

<footer>
  <div class="container" style="text-align:center; font-size:14px;">
    © <?= date('Y') ?> Администрирование системы
  </div>
</footer>

</body>
</html>
<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
/* ===== HTTP AUTH ===== */

if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('<h1>401 Требуется авторизация</h1>');
}

$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt = $db->prepare("SELECT pass_hash FROM admin WHERE login=?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (!$admin || !password_verify($_SERVER['PHP_AUTH_PW'], $admin['pass_hash'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('<h1>401 Неверные данные</h1>');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Админ-панель</title>
<link rel="stylesheet" href="styles.css">
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
}

.admin-table th,
.admin-table td {
    border: 1px solid #ddd;
    padding: 8px;
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

.edit-form {
    max-width: 600px;
    margin-bottom: 30px;
}

.edit-form input,
.edit-form select,
.edit-form textarea {
    width: 100%;
    margin-bottom: 10px;
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


</style>
</head>
<body>

<header class="admin-header">
  <div class="container">
    <h1 class="admin-main-title">Админ-панель</h1>
    <p class="admin-subtitle">Управление пользователями и статистикой</p>
  </div>
</header>

<main>
<div class="container admin-wrapper">
<?php

/* ===== УДАЛЕНИЕ ===== */
if (!empty($_GET['delete'])) {

    if (
        empty($_GET['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])
    ) {
        die("CSRF validation failed");
    }

    $id = (int)$_GET['delete'];

    $db->prepare("DELETE FROM application_language WHERE application_id=?")
       ->execute([$id]);

    $db->prepare("DELETE FROM application WHERE id=?")
       ->execute([$id]);

    header("Location: admin.php");
    exit();
}

/* ===== ОБНОВЛЕНИЕ ===== */

if (!empty($_POST['update'])) {

    $id = (int)$_POST['id'];

    $db->prepare("
        UPDATE application SET
        name=?, phone=?, email=?, birthdate=?, gender=?, bio=?
        WHERE id=?
    ")->execute([
        $_POST['fio'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        $id
    ]);

    $db->prepare("DELETE FROM application_language WHERE application_id=?")
       ->execute([$id]);

    $stmt = $db->prepare("
        INSERT INTO application_language (application_id, language_id)
        VALUES (?, ?)
    ");

    if (!empty($_POST['languages'])) {
        foreach ($_POST['languages'] as $lid) {
            $stmt->execute([$id, $lid]);
        }
    }

    header("Location: admin.php");
    exit();
}

/* ===== РЕЖИМ РЕДАКТИРОВАНИЯ ===== */

if (!empty($_GET['edit'])) {

    $id = (int)$_GET['edit'];

    $stmt = $db->prepare("SELECT * FROM application WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT language_id FROM application_language WHERE application_id=?");
    $stmt->execute([$id]);
    $user_langs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $langs = $db->query("SELECT id, name FROM language ORDER BY name")
                ->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <h1>Редактирование пользователя #<?= $id ?></h1>

    <form method="post" class="edit-form">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="update" value="1">

        Имя: <br>
        <input name="fio" value="<?= htmlspecialchars($user['name']) ?>"><br><br>

        Телефон:<br>
        <input name="phone" value="<?= htmlspecialchars($user['phone']) ?>"><br><br>

        Email:<br>
        <input name="email" value="<?= htmlspecialchars($user['email']) ?>"><br><br>

        Дата рождения:<br>
        <input type="date" name="birth_date" value="<?= $user['birthdate'] ?>"><br><br>

        Пол:<br>
        <select name="gender">
            <option value="male" <?= $user['gender']=='male'?'selected':'' ?>>Мужской</option>
            <option value="female" <?= $user['gender']=='female'?'selected':'' ?>>Женский</option>
        </select><br><br>

        Биография:<br>
        <textarea name="biography"><?= htmlspecialchars($user['bio']) ?></textarea><br><br>

        Языки:<br>
        <select name="languages[]" multiple>
            <?php foreach ($langs as $l): ?>
                <option value="<?= $l['id'] ?>"
                <?= in_array($l['id'], $user_langs)?'selected':'' ?>>
                <?= htmlspecialchars($l['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Сохранить изменения</button>
    </form>

    <br>
    <a href="admin.php">Назад</a>

    <?php
    exit();
}

/* ===== ВЫВОД ПОЛЬЗОВАТЕЛЕЙ ===== */

$users = $db->query("SELECT * FROM application ORDER BY id DESC")
            ->fetchAll(PDO::FETCH_ASSOC);

$stats = $db->query("
    SELECT l.name, COUNT(al.application_id) AS total
    FROM language l
    LEFT JOIN application_language al ON l.id = al.language_id
    GROUP BY l.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="admin-table">
<tr>
<th>ID</th>
<th>Имя</th>
<th>Email</th>
<th>Действия</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td class="admin-actions">
<a class="edit" href="admin.php?edit=<?= $u['id'] ?>">Редактировать</a>
<a class="delete"
   href="admin.php?delete=<?= $u['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
   onclick="return confirm('Удалить?')">
   Удалить
</a></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Статистика по языкам</h2>

<table class="admin-table">
<tr><th>Язык</th><th>Количество</th></tr>
<?php foreach ($stats as $s): ?>
<tr>
<td><?= htmlspecialchars($s['name']) ?></td>
<td><?= $s['total'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</main>

<footer>
  <div class="container" style="text-align:center; font-size:14px;">
    © <?= date('Y') ?> Администрирование системы
  </div>
</footer>


</body>
</html>

<?php
header('Content-Type: text/html; charset=UTF-8');

/* ===== HTTP AUTH ===== */

if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('<h1>401 Требуется авторизация</h1>');
}

/* ===== ПРОВЕРКА В БД ===== */

$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt = $db->prepare("SELECT pass_hash FROM admin WHERE login=?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($_SERVER['PHP_AUTH_PW'], $admin['pass_hash'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('<h1>401 Неверные данные</h1>');
}

/* ===== УДАЛЕНИЕ ===== */

if (!empty($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $db->prepare("DELETE FROM application_language WHERE application_id=?")
       ->execute([$id]);

    $db->prepare("DELETE FROM application WHERE id=?")
       ->execute([$id]);

    header("Location: admin.php");
    exit();
}

/* ===== ПОЛУЧЕНИЕ ВСЕХ ДАННЫХ ===== */

$users = $db->query("
    SELECT id, name, phone, email, birthdate, gender
    FROM application
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* ===== СТАТИСТИКА ===== */

$stats = $db->query("
    SELECT l.name, COUNT(al.application_id) AS total
    FROM language l
    LEFT JOIN application_language al ON l.id = al.language_id
    GROUP BY l.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Админ-панель</h1>

<h2>Пользователи</h2>

<table border="1" cellpadding="5">
<tr>
<th>ID</th>
<th>Имя</th>
<th>Телефон</th>
<th>Email</th>
<th>Дата рождения</th>
<th>Пол</th>
<th>Удалить</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= htmlspecialchars($u['phone']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= $u['birthdate'] ?></td>
<td><?= $u['gender'] ?></td>
<td>
<a href="admin.php?delete=<?= $u['id'] ?>"
onclick="return confirm('Удалить?')">Удалить</a>
</td>
</tr>
<?php endforeach; ?>

</table>

<h2>Статистика по языкам</h2>

<table border="1" cellpadding="5">
<tr>
<th>Язык</th>
<th>Количество пользователей</th>
</tr>

<?php foreach ($stats as $s): ?>
<tr>
<td><?= htmlspecialchars($s['name']) ?></td>
<td><?= $s['total'] ?></td>
</tr>
<?php endforeach; ?>

</table>

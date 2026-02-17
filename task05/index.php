<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

$errors = [];
$values = [];
$messages = [];

/* ===== СООБЩЕНИЕ ОБ УСПЕШНОМ СОХРАНЕНИИ ===== */
if (!empty($_COOKIE['save'])) {
    setcookie('save', '', time() - 3600);
    $messages[] = "Данные успешно сохранены.";

    if (!empty($_COOKIE['login']) && !empty($_COOKIE['pass'])) {
        $messages[] = sprintf(
            'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong>',
            htmlspecialchars($_COOKIE['login']),
            htmlspecialchars($_COOKIE['pass'])
        );
        setcookie('login', '', time() - 3600);
        setcookie('pass', '', time() - 3600);
    }
}

/* ===== ЧТЕНИЕ ОШИБОК ===== */
if (!empty($_COOKIE['errors'])) {
    $errors = json_decode($_COOKIE['errors'], true);
    setcookie('errors', '', time() - 3600);
}

/* ===== ЧТЕНИЕ СОХРАНЕННЫХ ЗНАЧЕНИЙ ===== */
if (!empty($_COOKIE['values'])) {
    $values = json_decode($_COOKIE['values'], true);
}

/* ===== ЕСЛИ ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН — ЗАГРУЖАЕМ ДАННЫЕ ===== */
if (!empty($_SESSION['login'])) {

    $db = new PDO(
        'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
        'webuser',
        'webpass',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $db->prepare("SELECT * FROM application WHERE id=?");
    $stmt->execute([$_SESSION['uid']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $values['fio'] = htmlspecialchars($user['name']);
        $values['phone'] = htmlspecialchars($user['phone']);
        $values['email'] = htmlspecialchars($user['email']);
        $values['birth_date'] = htmlspecialchars($user['birthdate']);
        $values['gender'] = htmlspecialchars($user['gender']);
        $values['biography'] = htmlspecialchars($user['bio']);

        $stmt = $db->prepare("
            SELECT language_id FROM application_language WHERE application_id=?
        ");
        $stmt->execute([$_SESSION['uid']]);
        $values['languages'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    $messages[] = "Вы вошли как <strong>" . htmlspecialchars($_SESSION['login']) . "</strong>";
}

/* ===== ПОДГРУЗКА ЯЗЫКОВ ===== */
$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$langs = $db->query("SELECT id, name FROM language ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Задание 5</title>
<link rel="stylesheet" href="styles.css">
<style>
.error { border:2px solid red !important; }
.message-success { color:green; margin-bottom:10px; }
.message-error { color:red; margin-bottom:5px; }
</style>
</head>

<body>

<header>
  <div class="container header-content">
    <div class="site-title">Задание 5 — авторизация</div>
  </div>
</header>

<main>
<div class="container content-wrapper">

<section id="form-section">

<?php
foreach ($messages as $m) {
    echo '<div class="message-success">'.$m.'</div>';
}

if (!empty($errors)) {
    foreach ($errors as $e) {
        echo '<div class="message-error">'.$e.'</div>';
    }
}
?>

<form method="post" action="form.php">

<div class="form-group">
<label>ФИО</label>
<input type="text" name="fio"
<?php if (!empty($errors['fio'])) print 'class="error"'; ?>
value="<?= htmlspecialchars($values['fio'] ?? '') ?>">
</div>

<div class="form-group">
<label>Телефон</label>
<input type="tel" name="phone"
<?php if (!empty($errors['phone'])) print 'class="error"'; ?>
value="<?= htmlspecialchars($values['phone'] ?? '') ?>">
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email"
<?php if (!empty($errors['email'])) print 'class="error"'; ?>
value="<?= htmlspecialchars($values['email'] ?? '') ?>">
</div>

<div class="form-group">
<label>Дата рождения</label>
<input type="date" name="birth_date"
<?php if (!empty($errors['birth_date'])) print 'class="error"'; ?>
value="<?= htmlspecialchars($values['birth_date'] ?? '') ?>">
</div>

<fieldset>
<legend>Пол</legend>
<label>
<input type="radio" name="gender" value="male"
<?= (($values['gender'] ?? '') == 'male') ? 'checked' : '' ?>>
Мужской
</label>

<label>
<input type="radio" name="gender" value="female"
<?= (($values['gender'] ?? '') == 'female') ? 'checked' : '' ?>>
Женский
</label>
</fieldset>

<div class="form-group">
<label>Любимые языки программирования</label>
<select name="languages[]" multiple>
<?php foreach ($langs as $l): ?>
<option value="<?= $l['id'] ?>"
<?= (isset($values['languages']) && in_array($l['id'], $values['languages'])) ? 'selected' : '' ?>>
<?= htmlspecialchars($l['name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="form-group">
<label>Биография</label>
<textarea name="biography"
<?php if (!empty($errors['biography'])) print 'class="error"'; ?>>
<?= htmlspecialchars($values['biography'] ?? '') ?>
</textarea>
</div>

<div class="form-group">
<label>
<input type="checkbox" name="contract_agreed" value="1"
<?= !empty($values['contract_agreed']) ? 'checked' : '' ?>>
С контрактом ознакомлен
</label>
</div>

<button type="submit">Сохранить</button>

</form>

<?php if (!empty($_SESSION['login'])): ?>
<br>
<form action="logout.php" method="post">
<button type="submit">Выйти</button>
</form>
<?php endif; ?>

</section>
</div>
</main>

<footer>
<div class="container">
Задание 5
</div>
</footer>

</body>
</html>

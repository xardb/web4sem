<?php
header('Content-Type: text/html; charset=UTF-8');

session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}
$errors = [];
$values = $_POST ?? [];

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("CSRF validation failed");
}

/* ===== ВАЛИДАЦИЯ ===== */

if (empty($_POST['fio'])) {
    $errors['fio'] = "Заполните ФИО";
}

if (empty($_POST['phone'])) {
    $errors['phone'] = "Заполните телефон";
}

if (empty($_POST['email'])) {
    $errors['email'] = "Заполните email";
}

if (empty($_POST['birth_date'])) {
    $errors['birth_date'] = "Заполните дату рождения";
}

if (empty($_POST['gender'])) {
    $errors['gender'] = "Выберите пол";
}

if (empty($_POST['languages'])) {
    $errors['languages'] = "Выберите язык";
}

if (empty($_POST['biography'])) {
    $errors['biography'] = "Заполните биографию";
}

if (empty($_POST['contract_agreed'])) {
    $errors['contract_agreed'] = "Необходимо согласие";
}

if (!empty($errors)) {
    setcookie('errors', json_encode($errors), 0);
    setcookie('values', json_encode($values), 0);
    header('Location: index.php');
    exit();
}

/* ===== ПОДКЛЮЧЕНИЕ К БД ===== */

$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

<?php
header('Content-Type: text/html; charset=UTF-8');

session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}
$errors = [];
$values = $_POST;

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("CSRF validation failed");
}

/* ===== ВАЛИДАЦИЯ ===== */

if (empty($_POST['fio'])) {
    $errors['fio'] = "Заполните ФИО";
}

if (empty($_POST['phone'])) {
    $errors['phone'] = "Заполните телефон";
}

if (empty($_POST['email'])) {
    $errors['email'] = "Заполните email";
}

if (empty($_POST['birth_date'])) {
    $errors['birth_date'] = "Заполните дату рождения";
}

if (empty($_POST['gender'])) {
    $errors['gender'] = "Выберите пол";
}

if (empty($_POST['languages'])) {
    $errors['languages'] = "Выберите язык";
}

if (empty($_POST['biography'])) {
    $errors['biography'] = "Заполните биографию";
}

if (empty($_POST['contract_agreed'])) {
    $errors['contract_agreed'] = "Необходимо согласие";
}

if (!empty($errors)) {
    setcookie('errors', json_encode($errors), 0);
    setcookie('values', json_encode($values), 0);
    header('Location: index.php');
    exit();
}

/* ===== ПОДКЛЮЧЕНИЕ К БД ===== */

$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

/* ===== ЕСЛИ ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН — ОБНОВЛЯЕМ ===== */
if (!empty($_SESSION['login'])) {

    $fio = trim($_POST['fio']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $biography = trim($_POST['biography']);
    $languages = $_POST['languages'];

    $stmt = $db->prepare("
        UPDATE application SET
        name=?, phone=?, email=?, birthdate=?, gender=?, bio=?
        WHERE id=?
    ");

    $stmt->execute([
        $fio,
        $phone,
        $email,
        $birth_date,
        $gender,
        $biography,
        $_SESSION['uid']
    ]);

    $db->prepare("DELETE FROM application_language WHERE application_id=?")
       ->execute([$_SESSION['uid']]);

    $stmt2 = $db->prepare("
        INSERT INTO application_language (application_id, language_id)
        VALUES (?, ?)
    ");

    foreach ($languages as $lid) {
        $stmt2->execute([$_SESSION['uid'], $lid]);
    }

    setcookie('save', '1');
    header('Location: index.php');
    exit();
}

/* ===== ЕСЛИ НОВЫЙ ПОЛЬЗОВАТЕЛЬ — СОЗДАЕМ ===== */

$login = substr(md5(uniqid()), 0, 8);
$pass_plain = substr(md5(rand()), 0, 8);
$pass_hash = password_hash($pass_plain, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    INSERT INTO application
    (name, phone, email, birthdate, gender, bio, contract, login, pass_hash)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST['fio'],
    $_POST['phone'],
    $_POST['email'],
    $_POST['birth_date'],
    $_POST['gender'],
    $_POST['biography'],
    1,
    $login,
    $pass_hash
]);

$appId = $db->lastInsertId();

$stmt2 = $db->prepare("
    INSERT INTO application_language (application_id, language_id)
    VALUES (?, ?)
");

foreach ($_POST['languages'] as $lid) {
    $stmt2->execute([$appId, $lid]);
}

/* ПОКАЗАТЬ ЛОГИН И ПАРОЛЬ ОДИН РАЗ */
setcookie('login', $login);
setcookie('pass', $pass_plain);
setcookie('save', '1');

header('Location: index.php');
exit();

/* ===== ЕСЛИ НОВЫЙ ПОЛЬЗОВАТЕЛЬ — СОЗДАЕМ ===== */

$login = substr(md5(uniqid()), 0, 8);
$pass_plain = substr(md5(rand()), 0, 8);
$pass_hash = password_hash($pass_plain, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    INSERT INTO application
    (name, phone, email, birthdate, gender, bio, contract, login, pass_hash)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST['fio'],
    $_POST['phone'],
    $_POST['email'],
    $_POST['birth_date'],
    $_POST['gender'],
    $_POST['biography'],
    1,
    $login,
    $pass_hash
]);

$appId = $db->lastInsertId();

$stmt2 = $db->prepare("
    INSERT INTO application_language (application_id, language_id)
    VALUES (?, ?)
");

foreach ($_POST['languages'] as $lid) {
    $stmt2->execute([$appId, $lid]);
}

/* ПОКАЗАТЬ ЛОГИН И ПАРОЛЬ ОДИН РАЗ */
setcookie('login', $login);
setcookie('pass', $pass_plain);
setcookie('save', '1');

header('Location: index.php');
exit();

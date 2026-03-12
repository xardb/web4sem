<?php
header('Content-Type: text/html; charset=UTF-8');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$errors = [];
$values = $_POST;

/* ===== CSRF ===== */

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("CSRF validation failed");
}

/* ===== ВАЛИДАЦИЯ ===== */

if (empty($_POST['fullName'])) {
    $errors['fullName'] = "Заполните ФИО";
}

if (empty($_POST['phone'])) {
    $errors['phone'] = "Заполните телефон";
}

if (empty($_POST['email'])) {
    $errors['email'] = "Заполните email";
}

if (empty($_POST['privacy'])) {
    $errors['privacy'] = "Необходимо согласие";
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

    $name = trim($_POST['fullName']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    $stmt = $db->prepare("
        UPDATE application SET
        name=?, phone=?, email=?, bio=?
        WHERE id=?
    ");

    $stmt->execute([
        $name,
        $phone,
        $email,
        $message,
        $_SESSION['uid']
    ]);

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
    (name, phone, email, bio, contract, login, pass_hash)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST['fullName'],
    $_POST['phone'],
    $_POST['email'],
    $_POST['message'],
    1,
    $login,
    $pass_hash
]);

/* ПОКАЗАТЬ ЛОГИН И ПАРОЛЬ ОДИН РАЗ */

setcookie('login', $login);
setcookie('pass', $pass_plain);
setcookie('save', '1');

header('Location: index.php');
exit();
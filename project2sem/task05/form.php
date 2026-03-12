<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit();
}

$errors = [];
$values = $_POST;

/* ===== CSRF ===== */

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    echo json_encode(["status" => "error", "message" => "CSRF validation failed"]);
    exit();
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
    echo json_encode([
        "status" => "error",
        "message" => "Ошибка валидации",
        "errors" => $errors
    ]);
    exit();
}

/* ===== ПОДКЛЮЧЕНИЕ К БД ===== */

try {

    $db = new PDO(
        'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
        'webuser',
        'webpass',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Ошибка подключения к БД"
    ]);

    exit();
}

/* ===== СОЗДАЕМ ЗАЯВКУ ===== */

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

setcookie('login', $login);
setcookie('pass', $pass_plain);
setcookie('save', '1');

/* ===== ОТВЕТ JS ===== */

echo json_encode([
    "status" => "success"
]);

exit();
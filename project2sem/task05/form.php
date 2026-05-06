<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$isAjax = (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
);

function send_json_response($status, $message = '', $extra = [], $httpCode = 200)
{
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode(array_merge([
        'status' => $status,
        'message' => $message
    ], $extra), JSON_UNESCAPED_UNICODE);

    exit();
}

function redirect_with_message($message)
{
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат отправки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main>
    <div class="container">
        <section>
            <h2>Результат</h2>
            <p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>
            <p><a href="../murlyka-shelter-main/index.php">Вернуться на главную</a></p>
        </section>
    </div>
</main>
</body>
</html>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        send_json_response('error', 'Метод не поддерживается', [], 405);
    }

    redirect_with_message('Метод не поддерживается.');
}

/* ===== CSRF ===== */

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    if ($isAjax) {
        send_json_response('error', 'Ошибка проверки безопасности формы', [], 403);
    }

    redirect_with_message('Ошибка проверки безопасности формы.');
}

/* ===== ПОЛУЧЕНИЕ ДАННЫХ ===== */

$fullName = trim($_POST['fullName'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$email    = trim($_POST['email'] ?? '');
$message  = trim($_POST['message'] ?? '');
$privacy  = isset($_POST['privacy']);

/* ===== ВАЛИДАЦИЯ ===== */

$errors = [];

if ($fullName === '') {
    $errors['fullName'] = 'Заполните ФИО';
}

if ($phone === '') {
    $errors['phone'] = 'Заполните телефон';
}

if ($email === '') {
    $errors['email'] = 'Заполните email';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Введите корректный email';
}

if (!$privacy) {
    $errors['privacy'] = 'Необходимо согласие на обработку персональных данных';
}

if (!empty($errors)) {
    if ($isAjax) {
        send_json_response('error', 'Ошибка валидации', [
            'errors' => $errors
        ], 422);
    }

    redirect_with_message('Ошибка валидации. Проверьте заполнение формы.');
}

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
    if ($isAjax) {
        send_json_response('error', 'Ошибка подключения к базе данных', [], 500);
    }

    redirect_with_message('Ошибка подключения к базе данных.');
}

/* ===== СОЗДАНИЕ ТАБЛИЦЫ, ЕСЛИ ЕЁ НЕТ ===== */

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS application (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            email VARCHAR(255) NOT NULL,
            bio TEXT NULL,
            contract TINYINT(1) NOT NULL DEFAULT 1,
            login VARCHAR(50) NOT NULL UNIQUE,
            pass_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch (PDOException $e) {
    if ($isAjax) {
        send_json_response('error', 'Ошибка подготовки таблицы application: ' . $e->getMessage(), [], 500);
    }

    redirect_with_message('Ошибка подготовки таблицы application.');
}

/* ===== СОЗДАНИЕ ЗАЯВКИ ===== */

try {
    do {
        $login = 'user_' . substr(bin2hex(random_bytes(4)), 0, 8);

        $checkStmt = $db->prepare("SELECT id FROM application WHERE login = ?");
        $checkStmt->execute([$login]);
        $exists = $checkStmt->fetch();
    } while ($exists);

    $plainPassword = substr(bin2hex(random_bytes(4)), 0, 8);
    $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $db->prepare("
        INSERT INTO application
            (name, phone, email, bio, contract, login, pass_hash)
        VALUES
            (:name, :phone, :email, :bio, :contract, :login, :pass_hash)
    ");

    $stmt->execute([
        ':name'      => $fullName,
        ':phone'     => $phone,
        ':email'     => $email,
        ':bio'       => $message,
        ':contract'  => 1,
        ':login'     => $login,
        ':pass_hash' => $passwordHash
    ]);

    $userId = $db->lastInsertId();

    $_SESSION['user_id'] = $userId;
    $_SESSION['login'] = $login;

    setcookie('login', $login, time() + 3600 * 24 * 30, '/');
    setcookie('pass', $plainPassword, time() + 3600 * 24 * 30, '/');
    setcookie('save', '1', time() + 3600 * 24 * 30, '/');

    $profileUrl = '../task05/profile.php?id=' . urlencode($userId);

    if ($isAjax) {
        send_json_response('success', 'Заявка успешно отправлена', [
            'login' => $login,
            'password' => $plainPassword,
            'profile_url' => $profileUrl
        ]);
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявка отправлена</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main>
    <div class="container">
        <section>
            <h2>Заявка успешно отправлена</h2>
            <p><strong>Логин:</strong> ' . htmlspecialchars($login, ENT_QUOTES, 'UTF-8') . '</p>
            <p><strong>Пароль:</strong> ' . htmlspecialchars($plainPassword, ENT_QUOTES, 'UTF-8') . '</p>
            <p><a href="' . htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') . '">Открыть профиль</a></p>
            <p><a href="../murlyka-shelter-main/index.php">Вернуться на главную</a></p>
        </section>
    </div>
</main>
</body>
</html>';
    exit();

} catch (PDOException $e) {
    if ($isAjax) {
        send_json_response('error', 'Ошибка сохранения заявки: ' . $e->getMessage(), [], 500);
    }

    redirect_with_message('Ошибка сохранения заявки.');
}
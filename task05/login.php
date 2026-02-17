<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stmt = $db->prepare("SELECT id, pass_hash FROM application WHERE login=?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['pass'], $user['pass_hash'])) {
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['uid'] = $user['id'];
        header('Location: index.php');
        exit();
    }
    else {
        echo "Неверный логин или пароль";
    }
}
?>

<form method="post">
    <input name="login" placeholder="Логин">
    <input name="pass" type="password" placeholder="Пароль">
    <button type="submit">Войти</button>
</form>

<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stmt = $db->prepare("SELECT id, pass_hash FROM application WHERE login=?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['pass'], $user['pass_hash'])) {
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['uid'] = $user['id'];
        header('Location: index.php');
        exit();
    } else {
        $error_message = "Неверный логин или пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Вход</title>
<link rel="stylesheet" href="styles.css">
<style>
.login-wrapper {
    max-width: 400px;
    margin: 50px auto;
}
.error-message {
    color: red;
    margin-bottom: 15px;
}
</style>
</head>

<body>

<header>
  <div class="container header-content">
    <div class="site-title">Вход в систему</div>
  </div>
</header>

<main>
  <div class="container">

    <div class="login-wrapper">

      <section>
        <h2>Авторизация</h2>

        <?php if (!empty($error_message)): ?>
          <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="post">

          <div class="form-group">
            <label>Логин</label>
            <input type="text" name="login" required>
          </div>

          <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="pass" required>
          </div>

          <button type="submit">Войти</button>

        </form>

      </section>

    </div>

  </div>
</main>

<footer>
  <div class="container">
    Задание 5
  </div>
</footer>

</body>
</html>

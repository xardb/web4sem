<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed");
    }

    $stmt = $db->prepare("SELECT id, pass_hash FROM application WHERE login=?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

          <div class="form-group">
            <label>Логин</label>
            <input type="text" name="login" required>
          </div>

          <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="pass" required>
          </div>

          <button type="submit">Войти</button>
          <div style="margin-top:15px;">
            <a href="index.php" style="text-decoration:none;">
              <button type="button" style="background-color:#6c757d;">
                Назад на главную
              </button>
            </a>
          </div>


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

<?php
$db = new PDO(
  'mysql:host=localhost;dbname=web4sem;charset=utf8',
  'webuser',
  'webpass',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]
);


$langs = $db->query("SELECT id, name FROM language ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Задание 3</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
  <div class="container header-content">
    <div class="site-title">Задание 3 — форма</div>
  </div>
</header>

<main>
  <div class="container content-wrapper">

    <section id="form-section">

      <?php
      if (!empty($_GET['save'])) {
        echo '<p style="color:green;font-weight:bold;">Данные сохранены</p>';
      }
      ?>

      <form method="post" action="form.php">

        <div class="form-group">
          <label>ФИО</label>
          <input type="text" name="fio" required>
        </div>

        <div class="form-group">
          <label>Телефон</label>
          <input type="tel" name="phone" required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>

        <div class="form-group">
          <label>Дата рождения</label>
          <input type="date" name="birth_date" required>
        </div>

        <fieldset>
          <legend>Пол</legend>

          <label>
            <input type="radio" name="gender" value="male" required>
            Мужской
          </label>

          <label>
            <input type="radio" name="gender" value="female">
            Женский
          </label>
        </fieldset>

        <div class="form-group">
          <label>Любимые языки программирования</label>
          <select name="languages[]" multiple required>
            <?php foreach ($langs as $l): ?>
              <option value="<?= $l['id'] ?>">
                <?= htmlspecialchars($l['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Биография</label>
          <textarea name="biography" required></textarea>
        </div>

        <div class="form-group">
          <label>
            <input type="checkbox" name="contract_agreed" value="1" required>
            С контрактом ознакомлен
          </label>
        </div>

        <button type="submit">Сохранить</button>

      </form>

    </section>

  </div>
</main>

<footer>
  <div class="container">
    Задание 3
  </div>
</footer>

</body>
</html>


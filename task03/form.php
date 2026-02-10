<?php
header('Content-Type: text/html; charset=UTF-8');

$errors = false;
/* ФИО */
if (
    empty($_POST['fio']) ||
    mb_strlen($_POST['fio']) > 150 ||
    !preg_match('/^[А-Яа-яA-Za-z ]+$/u', $_POST['fio'])
) {
    echo "ФИО должно содержать только буквы и пробелы и быть не длиннее 150 символов<br>";
    $errors = true;
}

/* Телефон */
if (
    empty($_POST['phone']) ||
    !preg_match('/^\+?[0-9]{10,15}$/', $_POST['phone'])
) {
    echo "Телефон должен содержать только цифры (допускается + в начале)<br>";
    $errors = true;
}

/* Email */
if (
    empty($_POST['email']) ||
    !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
) {
    echo "Некорректный email<br>";
    $errors = true;
}

/* Дата рождения */
if (empty($_POST['birth_date'])) {
    echo "Некорректная дата рождения<br>";
    $errors = true;
}

/* Пол */
if (
    empty($_POST['gender']) ||
    !in_array($_POST['gender'], ['male','female'])
) {
    echo "Некорректный пол<br>";
    $errors = true;
}

/* Языки */
if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    echo "Не выбраны языки программирования<br>";
    $errors = true;
}

/* Биография */
if (empty($_POST['biography'])) {
    echo "Биография не заполнена<br>";
    $errors = true;
}

/* Контракт */
if (empty($_POST['contract_agreed'])) {
    echo "Необходимо согласие с контрактом<br>";
    $errors = true;
}


if ($errors) {
  exit;
}

try {

  $db = new PDO(
    'mysql:host=localhost;dbname=web4sem;charset=utf8mb4',
    'webuser',
    'webpass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );


  $stmt = $db->prepare(
    "INSERT INTO application
    (name, phone, email, birthdate, gender, bio, contract)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
  );

  $stmt->execute([
    $_POST['fio'],
    $_POST['phone'],
    $_POST['email'],
    $_POST['birth_date'],
    $_POST['gender'],
    $_POST['biography'],
    1
  ]);

  $appId = $db->lastInsertId();

  $stmt2 = $db->prepare(
    "INSERT INTO application_language
     (application_id, language_id)
     VALUES (?, ?)"
  );

  foreach ($_POST['languages'] as $lid) {
    $stmt2->execute([$appId, $lid]);
  }

} catch (PDOException $e) {
  echo "Ошибка БД: ".$e->getMessage();
  exit;
}

header('Location: index.php?save=1');

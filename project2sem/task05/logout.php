<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

setcookie('login', '', time() - 3600, '/');
setcookie('pass', '', time() - 3600, '/');
setcookie('save', '', time() - 3600, '/');

header('Location: ../murlyka-shelter-main/index.php');
exit();
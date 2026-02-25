<?php

/**
 * Księgarnia ZSE - System zarządzania treścią
 * 
 * Autor: Michał Demus
 * Ostatnia modyfikacja: 2026
 */

// ============ SEKCJA 1: KONFIGURACJA I START SYSTEMU ============

define('BASE_PATH', __DIR__);

// Nagłówki bezpieczeństwa
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

require_once BASE_PATH . '/components/configuration.php';

// Konfiguracja bezpiecznych ciasteczek sesji
$is_secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure'   => $is_secure,
    'samesite' => 'Strict'
]);

session_start();

require_once BASE_PATH . '/components/functions.php';

// ============ SEKCJA 2: POŁĄCZENIE Z BAZĄ DANYCH ============

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysql = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    mysqli_set_charset($mysql, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Błąd połączenia z bazą danych. Spróbuj później.');
}


// ============ SEKCJA 3: INICJALIZACJA SYSTEMU ============

$is_login = false;
$sql_user = ['id' => 0];
$display = new Display();

// ============ SEKCJA 4: UWIERZYTELNIANIE UŻYTKOWNIKA ============

if (!empty($_SESSION['login_code'])) {
    // Pobierz dane użytkownika z bazy
    $stmt = mysqli_prepare($mysql, "SELECT * FROM `admin` WHERE `login_code` = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['login_code']);
    mysqli_stmt_execute($stmt);
    $sql_user = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if ($sql_user && empty($sql_user['banned'])) {
        // Zalogowany użytkownik
        $is_login = true;

        if (empty($_SESSION['login_regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['login_regenerated'] = true;
        }

        // Zapisz dane użytkownika w sesji
        $_SESSION['level']    = $sql_user['level'] ?? 0;
        $_SESSION['mail']     = $sql_user['mail'] ?? '';
        $_SESSION['name']     = $sql_user['name'] ?? '';
        $_SESSION['surname']  = $sql_user['surname'] ?? '';
        $_SESSION['state']    = $sql_user['state'] ?? '';
        $_SESSION['pesel']    = $sql_user['pesel'] ?? '';
        $_SESSION['address']  = $sql_user['address'] ?? '';
        $_SESSION['phone']    = $sql_user['phone'] ?? '';
    } else {
        // Wyloguj użytkownika
        $is_login = false;
        $sql_user = ['id' => 0];
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
    }
}


// ============ SEKCJA 5: ROUTING STRON ============

$allowed_pages = ['home', 'login', '404', 'logout'];
$page = filter_input(INPUT_GET, 'page') ?: 'home';

if (!in_array($page, $allowed_pages, true) || !file_exists(BASE_PATH . "/pages/{$page}.php")) {
    $page = '404';
    http_response_code(404);
}

include BASE_PATH . "/pages/{$page}.php";

// ============ SEKCJA 6: WYGENERUJ I WYŚWIETL STRONĘ ============

$template = new Template();
$template->setDefault();
$template->setMain($display->display());

mysqli_close($mysql);

echo $template->generateTemplate();

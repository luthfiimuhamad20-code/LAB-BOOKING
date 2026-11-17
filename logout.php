<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(is_logged_in() ? 'dashboard.php' : 'login.php');
}

verify_csrf();

$user = current_user();

if ($user !== null) {
    log_activity((int) $user['id'], 'logout', $user['name'] . ' keluar dari LAB BOOKING.');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $parameters = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $parameters['path'],
            'domain'   => $parameters['domain'],
            'secure'   => (bool) $parameters['secure'],
            'httponly' => (bool) $parameters['httponly'],
            'samesite' => $parameters['samesite'] ?: 'Lax',
        ]
    );
}

session_destroy();

redirect('login.php?logout=1');

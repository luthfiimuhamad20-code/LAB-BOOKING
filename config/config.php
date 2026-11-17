<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Identitas aplikasi
|--------------------------------------------------------------------------
| Nama resmi aplikasi adalah LAB BOOKING. Gunakan konstanta APP_NAME pada
| seluruh tampilan agar penamaan tetap konsisten.
*/
define('APP_NAME', 'LAB BOOKING');
define('APP_VERSION', '1.0.0');
define('APP_TAGLINE', 'Booking laboratorium jadi lebih mudah.');
define('APP_ORGANIZATION', 'PKK UPI');
define('APP_YEAR', '2026');

/*
|--------------------------------------------------------------------------
| Konfigurasi database
|--------------------------------------------------------------------------
| Sesuaikan DB_USER dan DB_PASS dengan pengaturan MySQL pada Laragon.
| Nama database tetap: lab_booking
*/
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'lab_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/*
|--------------------------------------------------------------------------
| Konfigurasi umum
|--------------------------------------------------------------------------
*/
define('APP_TIMEZONE', 'Asia/Jakarta');
define('APP_DEBUG', true);

date_default_timezone_set(APP_TIMEZONE);

/*
|--------------------------------------------------------------------------
| Sesi
|--------------------------------------------------------------------------
*/
if (session_status() === PHP_SESSION_NONE) {
    session_name('lab_booking_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/*
|--------------------------------------------------------------------------
| URL dasar aplikasi
|--------------------------------------------------------------------------
| Mendukung akses melalui virtual host Laragon (http://pkk_upi.test)
| maupun melalui subfolder (http://localhost/pkk_upi).
*/
$appRoot = str_replace('\\', '/', (string) realpath(dirname(__DIR__)));
$docRoot = isset($_SERVER['DOCUMENT_ROOT'])
    ? str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT']))
    : '';

$basePath = '';
if ($docRoot !== '' && $appRoot !== '' && stripos($appRoot, $docRoot) === 0) {
    $basePath = substr($appRoot, strlen($docRoot));
}

define('APP_ROOT', $appRoot);
define('BASE_URL', rtrim($basePath, '/'));

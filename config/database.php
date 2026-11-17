<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Mengembalikan koneksi PDO tunggal selama satu request.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $exception) {
        http_response_code(500);

        if (APP_DEBUG) {
            $message = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
            $database = htmlspecialchars(DB_NAME, ENT_QUOTES, 'UTF-8');
            echo '<!doctype html><html lang="id"><head><meta charset="utf-8">';
            echo '<title>Koneksi Database — LAB BOOKING</title>';
            echo '<style>body{font-family:system-ui,sans-serif;background:#f4f6fb;color:#101828;display:grid;place-items:center;min-height:100vh;margin:0}'
                . '.box{max-width:680px;background:#fff;padding:36px;border-radius:20px;border:1px solid #e4e7ec;box-shadow:0 20px 50px rgba(16,24,40,.08)}'
                . 'h1{font-size:22px;margin:0 0 8px}p{color:#667085;line-height:1.65}code{display:block;background:#101828;color:#d1fadf;padding:16px;border-radius:10px;margin-top:16px;white-space:pre-wrap;word-break:break-word}'
                . 'strong{color:#101828}</style></head><body><div class="box">';
            echo '<h1>LAB BOOKING belum dapat terhubung ke database</h1>';
            echo '<p>Pastikan MySQL pada Laragon sudah berjalan, database <strong>' . $database . '</strong> sudah dibuat, '
                . 'dan kredensial pada <strong>config/config.php</strong> sudah benar.</p>';
            echo '<p>Impor berkas <strong>database/lab_booking.sql</strong> melalui phpMyAdmin, lalu muat ulang halaman ini.</p>';
            echo '<code>' . $message . '</code></div></body></html>';
            exit;
        }

        exit('LAB BOOKING tidak dapat terhubung ke database. Silakan periksa konfigurasi.');
    }

    return $pdo;
}

<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$authTitle = $authTitle ?? 'Masuk — ' . APP_NAME;
$authDescription = $authDescription ?? 'LAB BOOKING — aplikasi pemesanan dan pengelolaan jadwal laboratorium PKK UPI.';
$flashes = get_flashes();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($authDescription) ?>">
    <meta name="theme-color" content="#0b1533">
    <meta name="application-name" content="LAB BOOKING">
    <title><?= e($authTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>?v=<?= e(APP_VERSION) ?>">
</head>
<body>
<div class="auth-page">
    <aside class="auth-visual" aria-label="Tentang LAB BOOKING">
        <div class="auth-brand">
            <span class="brand-mark"><?= icon('flask') ?></span>
            <div>
                <span class="auth-brand-name">LAB <b>BOOKING</b></span>
                <span class="auth-brand-sub"><?= e(APP_ORGANIZATION) ?></span>
            </div>
        </div>

        <div class="auth-hero">
            <span class="auth-eyebrow"><?= icon('sparkles') ?>Platform peminjaman laboratorium</span>
            <h1 class="auth-title">Booking lab kampus,<br><span>tanpa bentrok.</span></h1>
            <p class="auth-desc">
                LAB BOOKING membantu mahasiswa dan pengelola laboratorium mengajukan,
                memantau, serta mengatur jadwal pemakaian lab dalam satu tempat yang rapi.
            </p>

            <div class="auth-features">
                <div class="auth-feature">
                    <span class="auth-feature-icon"><?= icon('calendar-check') ?></span>
                    <div>
                        <strong>Ajukan booking dengan mudah</strong>
                        <span>Pilih lab, tanggal, dan waktu yang tersedia.</span>
                    </div>
                </div>
                <div class="auth-feature">
                    <span class="auth-feature-icon"><?= icon('shield') ?></span>
                    <div>
                        <strong>Persetujuan admin yang terkontrol</strong>
                        <span>Pantau status pengajuan hingga mendapatkan kepastian.</span>
                    </div>
                </div>
                <div class="auth-feature">
                    <span class="auth-feature-icon"><?= icon('calendar') ?></span>
                    <div>
                        <strong>Jadwal terpusat</strong>
                        <span>Semua agenda laboratorium dalam satu tampilan.</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="auth-preview" aria-hidden="true">
                <div class="auth-preview-head">
                    <span><?= icon('calendar') ?>Jadwal hari ini</span>
                    <small>LAB BOOKING</small>
                </div>
                <div class="auth-preview-row">
                    <span class="auth-preview-time">09.00</span>
                    <span class="auth-preview-main">
                        <strong>Lab Komputer 1</strong>
                        <span>Praktikum Pemrograman Web</span>
                    </span>
                    <span class="badge">Disetujui</span>
                </div>
                <div class="auth-preview-row">
                    <span class="auth-preview-time">13.00</span>
                    <span class="auth-preview-main">
                        <strong>Lab Multimedia</strong>
                        <span>Workshop Desain Interaktif</span>
                    </span>
                    <span class="badge">Menunggu</span>
                </div>
            </div>

            <div class="auth-visual-footer">
                <span><strong>LAB BOOKING</strong> · v<?= e(APP_VERSION) ?></span>
                <span><?= e(APP_ORGANIZATION) ?> · <?= e(APP_YEAR) ?></span>
            </div>
        </div>
    </aside>

    <main class="auth-panel">
        <div class="auth-card">
            <a class="auth-mobile-brand" href="<?= e(url('login.php')) ?>">
                <span class="brand-mark"><?= icon('flask') ?></span>
                <strong>LAB <b>BOOKING</b></strong>
            </a>

            <?php if ($flashes !== []): ?>
                <div class="auth-alert-stack" aria-live="polite">
                    <?php foreach ($flashes as $flash): ?>
                        <?php
                        $flashType = in_array($flash['type'], ['success', 'danger', 'warning', 'info'], true) ? $flash['type'] : 'info';
                        $flashIcon = [
                            'success' => 'check-circle',
                            'danger'  => 'alert-circle',
                            'warning' => 'alert-triangle',
                            'info'    => 'info',
                        ][$flashType];
                        ?>
                        <div class="alert alert-<?= e($flashType) ?>">
                            <?= icon($flashIcon) ?>
                            <span><?= e($flash['message']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

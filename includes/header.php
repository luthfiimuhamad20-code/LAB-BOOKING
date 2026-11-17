<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$user = current_user();

if ($user === null) {
    flash('warning', 'Silakan masuk terlebih dahulu untuk mengakses halaman tersebut.');
    redirect('login.php');
}

sync_completed_bookings();

$pageTitle = $pageTitle ?? 'Dashboard';
$active = $active ?? '';
$documentTitle = APP_NAME . ' — ' . $pageTitle;

$pendingApprovals = 0;
try {
    if ($user['role'] === 'admin') {
        $pendingApprovals = (int) q("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
    } else {
        $pendingApprovals = (int) q(
            "SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'pending'",
            [(int) $user['id']]
        )->fetchColumn();
    }
} catch (Throwable $exception) {
    $pendingApprovals = 0;
}

$flashes = get_flashes();
$navBase = 'app-nav-link';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="LAB BOOKING — aplikasi pemesanan dan pengelolaan jadwal laboratorium PKK UPI.">
    <meta name="theme-color" content="#0b1437">
    <meta name="application-name" content="LAB BOOKING">
    <title><?= e($documentTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>?v=<?= e(APP_VERSION) ?>">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <div class="sidebar-brand">
            <a class="brand" href="<?= e(url('dashboard.php')) ?>">
                <span class="brand-mark"><?= icon('flask') ?></span>
                <span class="brand-copy">
                    <span class="brand-name">LAB <b>BOOKING</b></span>
                    <span class="brand-sub"><?= e(APP_ORGANIZATION) ?></span>
                </span>
            </a>
            <button class="icon-btn sidebar-close" id="sidebarClose" type="button" aria-label="Tutup menu">
                <?= icon('x') ?>
            </button>
        </div>

        <div class="sidebar-scroll">
            <div class="nav-section">
                <span class="nav-label">Menu Utama</span>
                <nav class="nav">
                    <a class="<?= e($navBase . ($active === 'dashboard' ? ' is-active' : '')) ?>" href="<?= e(url('dashboard.php')) ?>">
                        <span class="nav-icon"><?= icon('dashboard') ?></span>
                        <span>Dashboard</span>
                    </a>
                    <a class="<?= e($navBase . ($active === 'bookings-create' ? ' is-active' : '')) ?>" href="<?= e(url('bookings/create.php')) ?>">
                        <span class="nav-icon"><?= icon('plus') ?></span>
                        <span>Buat Booking</span>
                    </a>
                    <a class="<?= e($navBase . ($active === 'bookings' ? ' is-active' : '')) ?>" href="<?= e(url('bookings/index.php')) ?>">
                        <span class="nav-icon"><?= icon('calendar-check') ?></span>
                        <span><?= $user['role'] === 'admin' ? 'Semua Booking' : 'Booking Saya' ?></span>
                        <?php if ($pendingApprovals > 0): ?>
                            <span class="nav-badge"><?= $pendingApprovals ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="<?= e($navBase . ($active === 'schedule' ? ' is-active' : '')) ?>" href="<?= e(url('schedule.php')) ?>">
                        <span class="nav-icon"><?= icon('calendar') ?></span>
                        <span>Jadwal Lab</span>
                    </a>
                </nav>
            </div>

            <div class="nav-section">
                <span class="nav-label">Laboratorium</span>
                <nav class="nav">
                    <a class="<?= e($navBase . ($active === 'labs' ? ' is-active' : '')) ?>" href="<?= e(url('labs/index.php')) ?>">
                        <span class="nav-icon"><?= icon('flask') ?></span>
                        <span><?= $user['role'] === 'admin' ? 'Manajemen Lab' : 'Daftar Lab' ?></span>
                    </a>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a class="<?= e($navBase . ($active === 'users' ? ' is-active' : '')) ?>" href="<?= e(url('users/index.php')) ?>">
                            <span class="nav-icon"><?= icon('users') ?></span>
                            <span>Pengguna</span>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="nav-section">
                <span class="nav-label">Informasi</span>
                <nav class="nav">
                    <a class="<?= e($navBase . ($active === 'about' ? ' is-active' : '')) ?>" href="<?= e(url('about.php')) ?>">
                        <span class="nav-icon"><?= icon('info') ?></span>
                        <span>Informasi Aplikasi</span>
                    </a>
                    <a class="<?= e($navBase . ($active === 'profile' ? ' is-active' : '')) ?>" href="<?= e(url('profile.php')) ?>">
                        <span class="nav-icon"><?= icon('user') ?></span>
                        <span>Profil Saya</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-note">
                <span class="sidebar-note-icon"><?= icon('help') ?></span>
                <strong>Butuh panduan?</strong>
                <p>Pelajari cara mengajukan dan mengelola booking laboratorium.</p>
                <a href="<?= e(url('about.php')) ?>">Buka informasi aplikasi <?= icon('arrow-right') ?></a>
            </div>
            <div class="sidebar-foot">
                <span>LAB BOOKING</span>
                <span>v<?= e(APP_VERSION) ?></span>
            </div>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="icon-btn hamburger" id="sidebarToggle" type="button" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                    <?= icon('menu') ?>
                </button>
                <div class="topbar-crumb">
                    <a href="<?= e(url('dashboard.php')) ?>">LAB BOOKING</a>
                    <span class="crumb-sep">/</span>
                    <strong><?= e($pageTitle) ?></strong>
                </div>
            </div>
            <div class="topbar-right">
                <span class="topbar-date"><?= icon('calendar') ?><?= e(format_date(date('Y-m-d'), 'short', true)) ?></span>

                <?php if ($user['role'] === 'admin' && $pendingApprovals > 0): ?>
                    <a class="icon-btn notification-btn" href="<?= e(url('bookings/index.php?status=pending')) ?>" title="<?= $pendingApprovals ?> booking menunggu persetujuan" aria-label="<?= $pendingApprovals ?> booking menunggu persetujuan">
                        <?= icon('bell') ?>
                        <span class="notification-dot"><?= $pendingApprovals > 9 ? '9+' : $pendingApprovals ?></span>
                    </a>
                <?php endif; ?>

                <div class="profile" data-dropdown>
                    <button class="profile-btn" type="button" data-dropdown-toggle aria-expanded="false" aria-haspopup="true">
                        <?= avatar($user['name']) ?>
                        <span class="profile-info">
                            <strong><?= e($user['name']) ?></strong>
                            <small><?= e(role_label($user['role'])) ?></small>
                        </span>
                        <?= icon('chevron-down', 'profile-chevron') ?>
                    </button>
                    <div class="profile-menu" data-dropdown-menu hidden>
                        <div class="profile-menu-head">
                            <strong><?= e($user['name']) ?></strong>
                            <span><?= e($user['email']) ?></span>
                        </div>
                        <a href="<?= e(url('profile.php')) ?>" role="menuitem"><?= icon('user') ?>Profil Saya</a>
                        <a href="<?= e(url('about.php')) ?>" role="menuitem"><?= icon('info') ?>Informasi Aplikasi</a>
                        <div class="profile-menu-divider"></div>
                        <form method="post" action="<?= e(url('logout.php')) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" role="menuitem"><?= icon('log-out') ?>Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <?php if ($flashes !== []): ?>
            <div class="flash-stack" aria-live="polite">
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
                    <div class="flash flash-<?= e($flashType) ?>" role="status">
                        <span class="flash-icon"><?= icon($flashIcon) ?></span>
                        <span class="flash-text"><?= e($flash['message']) ?></span>
                        <button type="button" class="flash-close" data-dismiss-flash aria-label="Tutup notifikasi"><?= icon('x') ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="main-content" id="main-content">

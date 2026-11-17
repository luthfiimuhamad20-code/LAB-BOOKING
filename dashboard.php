<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$isAdmin = $user['role'] === 'admin';
$firstName = explode(' ', trim($user['name']))[0];

$pageTitle = $isAdmin ? 'Dashboard Admin' : 'Dashboard Pengguna';
$active = 'dashboard';

if ($isAdmin) {
    $totalLabs = (int) q("SELECT COUNT(*) FROM labs WHERE status = 'active'")->fetchColumn();
    $pendingCount = (int) q("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
    $todayCount = (int) q(
        "SELECT COUNT(*) FROM bookings WHERE booking_date = CURDATE() AND status IN ('approved', 'completed')"
    )->fetchColumn();
    $totalUsers = (int) q("SELECT COUNT(*) FROM users WHERE role = 'user' AND status = 'active'")->fetchColumn();

    $pendingBookings = q(
        "SELECT b.*, l.name AS lab_name, l.code AS lab_code, u.name AS user_name, u.email AS user_email
         FROM bookings b
         JOIN labs l ON l.id = b.lab_id
         JOIN users u ON u.id = b.user_id
         WHERE b.status = 'pending'
         ORDER BY b.created_at ASC, b.id ASC
         LIMIT 5"
    )->fetchAll();

    $todayAgenda = q(
        "SELECT b.*, l.name AS lab_name, l.code AS lab_code, u.name AS user_name
         FROM bookings b
         JOIN labs l ON l.id = b.lab_id
         JOIN users u ON u.id = b.user_id
         WHERE b.booking_date = CURDATE()
           AND b.status IN ('pending', 'approved', 'completed')
         ORDER BY b.start_time ASC
         LIMIT 6"
    )->fetchAll();

    $recentActivities = q(
        "SELECT a.*, u.name AS user_name
         FROM activity_logs a
         LEFT JOIN users u ON u.id = a.user_id
         ORDER BY a.created_at DESC, a.id DESC
         LIMIT 6"
    )->fetchAll();

    $chartStart = new DateTimeImmutable('today -6 days');
    $chartRows = q(
        "SELECT DATE(created_at) AS booking_day, COUNT(*) AS total
         FROM bookings
         WHERE created_at >= ?
         GROUP BY DATE(created_at)",
        [$chartStart->format('Y-m-d') . ' 00:00:00']
    )->fetchAll();

    $chartMap = [];
    foreach ($chartRows as $row) {
        $chartMap[$row['booking_day']] = (int) $row['total'];
    }

    $dayShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $chart = [];
    for ($index = 0; $index < 7; $index++) {
        $date = $chartStart->modify('+' . $index . ' days');
        $key = $date->format('Y-m-d');
        $chart[] = [
            'label'   => $dayShort[(int) $date->format('w')],
            'date'    => format_date($key),
            'count'   => $chartMap[$key] ?? 0,
            'isToday' => $key === date('Y-m-d'),
        ];
    }

    $chartTotal = array_sum(array_column($chart, 'count'));
    $chartMax = max(1, max(array_column($chart, 'count')));
} else {
    $myStatusRows = q(
        'SELECT status, COUNT(*) AS total FROM bookings WHERE user_id = ? GROUP BY status',
        [(int) $user['id']]
    )->fetchAll();

    $myCounts = array_fill_keys(array_keys(booking_statuses()), 0);
    foreach ($myStatusRows as $row) {
        if (isset($myCounts[$row['status']])) {
            $myCounts[$row['status']] = (int) $row['total'];
        }
    }

    $myTotal = array_sum($myCounts);
    $myPending = $myCounts['pending'];
    $myApproved = $myCounts['approved'];
    $myCompleted = $myCounts['completed'];

    $myUpcoming = q(
        "SELECT b.*, l.name AS lab_name, l.code AS lab_code, l.location AS lab_location
         FROM bookings b
         JOIN labs l ON l.id = b.lab_id
         WHERE b.user_id = ?
           AND b.booking_date >= CURDATE()
           AND b.status IN ('pending', 'approved')
         ORDER BY b.booking_date ASC, b.start_time ASC
         LIMIT 4",
        [(int) $user['id']]
    )->fetchAll();

    $myRecent = q(
        'SELECT b.*, l.name AS lab_name, l.code AS lab_code
         FROM bookings b
         JOIN labs l ON l.id = b.lab_id
         WHERE b.user_id = ?
         ORDER BY b.created_at DESC, b.id DESC
         LIMIT 5',
        [(int) $user['id']]
    )->fetchAll();
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($isAdmin): ?>
    <div class="page-head">
        <div class="page-head-text">
            <span class="page-eyebrow"><?= icon('dashboard') ?>Panel Administrator</span>
            <h1 class="page-title">Dashboard Admin</h1>
            <p class="page-subtitle">
                Ringkasan operasional laboratorium · <?= e(format_date(date('Y-m-d'), 'long', true)) ?>.
            </p>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="<?= e(url('labs/index.php')) ?>"><?= icon('flask') ?>Kelola Lab</a>
            <a class="btn btn-primary" href="<?= e(url('bookings/create.php')) ?>"><?= icon('plus') ?>Buat Booking</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card stat-info">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('flask') ?></span>
                <span class="stat-trend">Tersedia</span>
            </div>
            <span class="stat-value"><?= $totalLabs ?></span>
            <span class="stat-label">Laboratorium aktif</span>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('clock') ?></span>
                <span class="stat-trend">Perlu ditinjau</span>
            </div>
            <span class="stat-value"><?= $pendingCount ?></span>
            <span class="stat-label">Booking menunggu</span>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('calendar-check') ?></span>
                <span class="stat-trend">Hari ini</span>
            </div>
            <span class="stat-value"><?= $todayCount ?></span>
            <span class="stat-label">Booking berjalan</span>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('users') ?></span>
                <span class="stat-trend">Aktif</span>
            </div>
            <span class="stat-value"><?= $totalUsers ?></span>
            <span class="stat-label">Pengguna terdaftar</span>
        </div>
    </div>

    <div class="dash-grid">
        <div class="dash-stack">
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Permintaan Persetujuan</h2>
                        <p class="card-subtitle">Pengajuan terbaru yang menunggu keputusan admin.</p>
                    </div>
                    <a class="link-arrow" href="<?= e(url('bookings/index.php?status=pending')) ?>">Lihat semua <?= icon('arrow-right') ?></a>
                </div>

                <?php if ($pendingBookings === []): ?>
                    <div class="empty-state" style="padding:36px 24px">
                        <div class="empty-icon"><?= icon('check-circle') ?></div>
                        <h3>Tidak ada permintaan baru</h3>
                        <p>Semua pengajuan booking sudah ditinjau. Kerja bagus!</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Laboratorium</th>
                                    <th>Jadwal</th>
                                    <th style="text-align:right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div class="table-user">
                                                <?= avatar($booking['user_name'], 'avatar-sm') ?>
                                                <div class="table-user-info">
                                                    <strong><?= e($booking['user_name']) ?></strong>
                                                    <span><?= e($booking['user_email']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="cell-main"><?= e($booking['lab_name']) ?></span>
                                            <span class="cell-sub"><?= e($booking['lab_code']) ?></span>
                                        </td>
                                        <td>
                                            <span class="cell-main"><?= e(format_date($booking['booking_date'])) ?></span>
                                            <span class="cell-sub"><?= e(format_time_range($booking['start_time'], $booking['end_time'])) ?></span>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a class="btn btn-sm btn-soft" href="<?= e(url('bookings/view.php?id=' . (int) $booking['id'])) ?>">Tinjau</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Aktivitas Booking</h2>
                        <p class="card-subtitle">Jumlah booking yang diajukan dalam 7 hari terakhir.</p>
                    </div>
                    <span class="badge badge-primary"><?= $chartTotal ?> booking</span>
                </div>
                <div class="card-body">
                    <div class="chart-box" aria-label="Grafik booking tujuh hari terakhir">
                        <?php foreach ($chart as $bar): ?>
                            <div class="chart-col<?= $bar['isToday'] ? ' is-today' : '' ?>" title="<?= e($bar['date']) ?>: <?= $bar['count'] ?> booking">
                                <span class="chart-value"><?= $bar['count'] ?></span>
                                <div class="chart-track">
                                    <div class="chart-bar" style="height: <?= $bar['count'] > 0 ? max(8, (int) round($bar['count'] / $chartMax * 100)) : 3 ?>%"></div>
                                </div>
                                <span class="chart-label"><?= e($bar['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>

        <div class="dash-stack">
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Agenda Hari Ini</h2>
                        <p class="card-subtitle"><?= e(format_date(date('Y-m-d'), 'long')) ?></p>
                    </div>
                    <a class="link-arrow" href="<?= e(url('schedule.php')) ?>">Jadwal <?= icon('arrow-right') ?></a>
                </div>
                <div class="card-body">
                    <?php if ($todayAgenda === []): ?>
                        <div class="empty-state" style="padding:26px 10px 18px">
                            <div class="empty-icon"><?= icon('calendar') ?></div>
                            <h3>Belum ada agenda</h3>
                            <p>Tidak ada pemakaian laboratorium yang terjadwal hari ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="agenda-list">
                            <?php foreach ($todayAgenda as $agenda): ?>
                                <div class="agenda-item">
                                    <div class="agenda-time">
                                        <?= e(format_time($agenda['start_time'])) ?>
                                        <span><?= e(format_time($agenda['end_time'])) ?></span>
                                    </div>
                                    <div class="agenda-main">
                                        <strong><?= e($agenda['lab_name']) ?></strong>
                                        <small><?= icon('user') ?><?= e($agenda['user_name']) ?></small>
                                    </div>
                                    <?= booking_status_badge($agenda['status']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Aktivitas Terbaru</h2>
                        <p class="card-subtitle">Jejak aktivitas pengguna di LAB BOOKING.</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($recentActivities === []): ?>
                        <p class="text-muted" style="font-size:11px">Belum ada aktivitas tercatat.</p>
                    <?php else: ?>
                        <?php
                        $activityIcons = [
                            'login'            => 'key',
                            'logout'           => 'log-out',
                            'register'         => 'user-check',
                            'booking_created'  => 'calendar-check',
                            'booking_approved' => 'check-circle',
                            'booking_rejected' => 'x-circle',
                            'booking_cancelled'=> 'slash',
                            'lab_created'      => 'flask',
                            'lab_updated'      => 'edit',
                            'lab_deleted'      => 'trash',
                            'user_created'     => 'users',
                            'user_updated'     => 'user',
                            'user_deleted'     => 'trash',
                        ];
                        ?>
                        <div class="activity-list">
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="activity-item">
                                    <span class="activity-icon"><?= icon($activityIcons[$activity['action']] ?? 'activity') ?></span>
                                    <div class="activity-main">
                                        <p><?= e($activity['description']) ?></p>
                                        <span><?= e(format_datetime($activity['created_at'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
<?php else: ?>
    <div class="page-head">
        <div class="page-head-text">
            <span class="page-eyebrow"><?= icon('sparkles') ?>Ruang pengguna</span>
            <h1 class="page-title">Dashboard Pengguna</h1>
            <p class="page-subtitle">Ringkasan booking dan jadwal laboratorium Anda di LAB BOOKING.</p>
        </div>
    </div>

    <section class="hero-panel">
        <div class="hero-copy">
            <span class="hero-eyebrow"><?= icon('flask') ?><?= e(greeting()) ?>, <?= e($firstName) ?></span>
            <h2 class="hero-title">Butuh laboratorium untuk kegiatan Anda?</h2>
            <p class="hero-text">
                Ajukan booking sekarang, pilih waktu yang tersedia, lalu pantau persetujuan dari admin
                langsung melalui dashboard LAB BOOKING.
            </p>
            <div class="hero-stats">
                <div class="hero-stat">
                    <strong><?= $myTotal ?></strong>
                    <span>Total booking</span>
                </div>
                <div class="hero-stat">
                    <strong><?= $myPending ?></strong>
                    <span>Menunggu</span>
                </div>
                <div class="hero-stat">
                    <strong><?= $myApproved + $myCompleted ?></strong>
                    <span>Disetujui</span>
                </div>
            </div>
        </div>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(url('bookings/create.php')) ?>"><?= icon('plus') ?>Buat Booking</a>
            <a class="btn btn-ghost-light" href="<?= e(url('schedule.php')) ?>"><?= icon('calendar') ?>Lihat Jadwal</a>
        </div>
    </section>

    <div class="stat-grid">
        <div class="stat-card stat-primary">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('calendar-check') ?></span>
                <span class="stat-trend">Semua</span>
            </div>
            <span class="stat-value"><?= $myTotal ?></span>
            <span class="stat-label">Total booking saya</span>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('clock') ?></span>
                <span class="stat-trend">Menunggu</span>
            </div>
            <span class="stat-value"><?= $myPending ?></span>
            <span class="stat-label">Menunggu persetujuan</span>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('check-circle') ?></span>
                <span class="stat-trend">Aktif</span>
            </div>
            <span class="stat-value"><?= $myApproved ?></span>
            <span class="stat-label">Booking disetujui</span>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-top">
                <span class="stat-icon"><?= icon('flag') ?></span>
                <span class="stat-trend">Selesai</span>
            </div>
            <span class="stat-value"><?= $myCompleted ?></span>
            <span class="stat-label">Booking selesai</span>
        </div>
    </div>

    <div class="dash-grid">
        <div class="dash-stack">
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Booking Mendatang</h2>
                        <p class="card-subtitle">Jadwal yang sedang menunggu atau sudah disetujui.</p>
                    </div>
                    <a class="link-arrow" href="<?= e(url('bookings/index.php')) ?>">Booking saya <?= icon('arrow-right') ?></a>
                </div>
                <div class="card-body">
                    <?php if ($myUpcoming === []): ?>
                        <div class="empty-state" style="padding:32px 14px">
                            <div class="empty-icon"><?= icon('calendar') ?></div>
                            <h3>Belum ada booking mendatang</h3>
                            <p>Rencanakan pemakaian laboratorium Anda dengan mengajukan booking baru.</p>
                            <a class="btn btn-primary btn-sm" href="<?= e(url('bookings/create.php')) ?>"><?= icon('plus') ?>Buat Booking</a>
                        </div>
                    <?php else: ?>
                        <div class="agenda-list">
                            <?php foreach ($myUpcoming as $booking): ?>
                                <div class="agenda-item">
                                    <div class="agenda-time">
                                        <?= e(format_time($booking['start_time'])) ?>
                                        <span><?= e(format_time($booking['end_time'])) ?></span>
                                    </div>
                                    <div class="agenda-main">
                                        <strong><?= e($booking['lab_name']) ?></strong>
                                        <small><?= icon('calendar') ?><?= e(format_date($booking['booking_date'], 'short', true)) ?></small>
                                    </div>
                                    <?= booking_status_badge($booking['status']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Booking Terbaru</h2>
                        <p class="card-subtitle">Riwayat pengajuan terakhir Anda.</p>
                    </div>
                </div>
                <?php if ($myRecent === []): ?>
                    <div class="empty-state" style="padding:34px 20px">
                        <div class="empty-icon"><?= icon('clipboard') ?></div>
                        <h3>Riwayat masih kosong</h3>
                        <p>Booking pertama Anda akan muncul di sini setelah diajukan.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Laboratorium</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myRecent as $booking): ?>
                                    <tr>
                                        <td>
                                            <a class="table-code" href="<?= e(url('bookings/view.php?id=' . (int) $booking['id'])) ?>">
                                                <?= e($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="cell-main"><?= e($booking['lab_name']) ?></span>
                                            <span class="cell-sub"><?= e($booking['lab_code']) ?></span>
                                        </td>
                                        <td>
                                            <span class="cell-main"><?= e(format_date($booking['booking_date'])) ?></span>
                                            <span class="cell-sub"><?= e(format_time_range($booking['start_time'], $booking['end_time'])) ?></span>
                                        </td>
                                        <td><?= booking_status_badge($booking['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="dash-stack">
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Aksi Cepat</h2>
                        <p class="card-subtitle">Pintasan yang sering Anda gunakan.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a class="quick-action" href="<?= e(url('bookings/create.php')) ?>">
                            <span class="quick-action-icon"><?= icon('plus') ?></span>
                            <span>
                                <strong>Buat Booking</strong>
                                <span>Ajukan jadwal lab baru</span>
                            </span>
                        </a>
                        <a class="quick-action" href="<?= e(url('schedule.php')) ?>">
                            <span class="quick-action-icon"><?= icon('calendar') ?></span>
                            <span>
                                <strong>Jadwal Lab</strong>
                                <span>Lihat agenda hari ini</span>
                            </span>
                        </a>
                        <a class="quick-action" href="<?= e(url('labs/index.php')) ?>">
                            <span class="quick-action-icon"><?= icon('flask') ?></span>
                            <span>
                                <strong>Daftar Lab</strong>
                                <span>Jelajahi fasilitas lab</span>
                            </span>
                        </a>
                        <a class="quick-action" href="<?= e(url('profile.php')) ?>">
                            <span class="quick-action-icon"><?= icon('user') ?></span>
                            <span>
                                <strong>Profil Saya</strong>
                                <span>Perbarui data akun</span>
                            </span>
                        </a>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Ringkasan Status</h2>
                        <p class="card-subtitle">Distribusi status booking Anda.</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $statusMeta = [
                        'pending'   => ['label' => 'Menunggu', 'class' => 'is-warning'],
                        'approved'  => ['label' => 'Disetujui', 'class' => 'is-success'],
                        'completed' => ['label' => 'Selesai', 'class' => 'is-info'],
                        'rejected'  => ['label' => 'Ditolak', 'class' => 'is-danger'],
                        'cancelled' => ['label' => 'Dibatalkan', 'class' => ''],
                    ];
                    ?>
                    <div class="status-meter-list">
                        <?php foreach ($statusMeta as $statusKey => $meta): ?>
                            <?php $count = $myCounts[$statusKey] ?? 0; ?>
                            <div class="status-meter">
                                <div class="status-meter-head">
                                    <span><?= e($meta['label']) ?></span>
                                    <strong><?= $count ?></strong>
                                </div>
                                <div class="status-meter-bar">
                                    <div class="status-meter-fill <?= e($meta['class']) ?>" style="width: <?= $myTotal > 0 ? max(2, (int) round($count / $myTotal * 100)) : 0 ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="booking-note" style="margin-top:16px">
                        <?= icon('info') ?>
                        <span>Booking berstatus <strong>Menunggu</strong> akan tampil pada agenda admin untuk segera ditinjau.</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

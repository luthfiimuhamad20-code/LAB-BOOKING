<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_login();

$user = current_user();
$isAdmin = $user['role'] === 'admin';
$labId = (int) get('id');

if ($labId <= 0) {
    flash('danger', 'Laboratorium yang Anda cari tidak ditemukan.');
    redirect('labs/index.php');
}

$lab = q('SELECT * FROM labs WHERE id = ? LIMIT 1', [$labId])->fetch();

if (!$lab) {
    flash('danger', 'Laboratorium yang Anda cari tidak ditemukan.');
    redirect('labs/index.php');
}

$totalBookings = (int) q('SELECT COUNT(*) FROM bookings WHERE lab_id = ?', [$labId])->fetchColumn();
$completedBookings = (int) q(
    "SELECT COUNT(*) FROM bookings WHERE lab_id = ? AND status = 'completed'",
    [$labId]
)->fetchColumn();

$allowedStatuses = $isAdmin ? ['pending', 'approved'] : ['approved'];
$statusPlaceholders = implode(',', array_fill(0, count($allowedStatuses), '?'));

$upcoming = q(
    "SELECT b.*, u.name AS user_name
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     WHERE b.lab_id = ?
       AND b.booking_date >= CURDATE()
       AND b.status IN ($statusPlaceholders)
     ORDER BY b.booking_date ASC, b.start_time ASC
     LIMIT 8",
    array_merge([$labId], $allowedStatuses)
)->fetchAll();

$facilities = facility_list($lab['facilities']);
$pageTitle = $lab['name'];
$active = 'labs';

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('flask') ?>Detail Laboratorium</span>
        <h1 class="page-title"><?= e($lab['name']) ?></h1>
        <p class="page-subtitle"><?= e($lab['location']) ?> · Kode <?= e($lab['code']) ?> · Kapasitas <?= (int) $lab['capacity'] ?> orang.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('labs/index.php')) ?>"><?= icon('arrow-left') ?>Kembali</a>
        <?php if ($isAdmin): ?>
            <a class="btn btn-primary" href="<?= e(url('labs/edit.php?id=' . (int) $lab['id'])) ?>"><?= icon('edit') ?>Edit Lab</a>
        <?php elseif ($lab['status'] === 'active'): ?>
            <a class="btn btn-primary" href="<?= e(url('bookings/create.php?lab_id=' . (int) $lab['id'])) ?>"><?= icon('plus') ?>Booking Lab Ini</a>
        <?php endif; ?>
    </div>
</div>

<div class="detail-grid">
    <div class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Tentang Laboratorium</h2>
                    <p class="card-subtitle">Informasi umum dan fasilitas yang tersedia.</p>
                </div>
                <?= lab_status_badge($lab['status']) ?>
            </div>
            <div class="card-body">
                <p class="prose-block"><?= e($lab['description'] ?: 'Belum ada deskripsi untuk laboratorium ini.') ?></p>

                <div class="form-section" style="margin-top:20px">
                    <div class="form-section-title"><?= icon('layers') ?>Fasilitas lab</div>
                    <?php if ($facilities !== []): ?>
                        <div class="chip-row">
                            <?php foreach ($facilities as $facility): ?>
                                <span class="chip"><?= icon('check') ?><?= e($facility) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="field-hint">Fasilitas laboratorium belum dicatat.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Jadwal Mendatang</h2>
                    <p class="card-subtitle">
                        <?= $isAdmin
                            ? 'Termasuk pengajuan yang masih menunggu persetujuan.'
                            : 'Jadwal yang telah disetujui untuk laboratorium ini.' ?>
                    </p>
                </div>
                <a class="link-arrow" href="<?= e(url('schedule.php?lab_id=' . (int) $lab['id'])) ?>">Lihat jadwal <?= icon('arrow-right') ?></a>
            </div>
            <?php if ($upcoming === []): ?>
                <div class="empty-state" style="padding:36px 22px">
                    <div class="empty-icon"><?= icon('calendar') ?></div>
                    <h3>Belum ada jadwal mendatang</h3>
                    <p>Laboratorium ini masih tersedia untuk diajukan pada tanggal yang Anda inginkan.</p>
                    <?php if ($lab['status'] === 'active' && !$isAdmin): ?>
                        <a class="btn btn-primary btn-sm" href="<?= e(url('bookings/create.php?lab_id=' . (int) $lab['id'])) ?>"><?= icon('plus') ?>Ajukan booking</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="schedule-list">
                    <?php foreach ($upcoming as $booking): ?>
                        <div class="schedule-row">
                            <div class="schedule-row-time<?= $booking['status'] === 'pending' ? ' is-pending' : '' ?>">
                                <strong><?= e(format_time_range($booking['start_time'], $booking['end_time'])) ?></strong>
                                <span><?= e(format_date($booking['booking_date'])) ?></span>
                            </div>
                            <div class="schedule-row-main">
                                <strong><?= e($booking['purpose']) ?></strong>
                                <p><?= icon('user') ?> <?= e($booking['user_name']) ?> · <?= (int) $booking['participants'] ?> peserta</p>
                            </div>
                            <div class="schedule-row-meta">
                                <?= booking_status_badge($booking['status']) ?>
                                <?php if ($isAdmin): ?>
                                    <a class="btn btn-icon btn-sm btn-secondary" href="<?= e(url('bookings/view.php?id=' . (int) $booking['id'])) ?>" title="Lihat booking" aria-label="Lihat booking">
                                        <?= icon('arrow-up-right') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Informasi Lab</h2>
                    <p class="card-subtitle">Data operasional laboratorium.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-table">
                    <div class="info-row">
                        <span>Kode lab</span>
                        <strong><?= e($lab['code']) ?></strong>
                    </div>
                    <div class="info-row">
                        <span>Lokasi</span>
                        <strong><?= e($lab['location']) ?></strong>
                    </div>
                    <div class="info-row">
                        <span>Kapasitas</span>
                        <strong><?= (int) $lab['capacity'] ?> orang</strong>
                    </div>
                    <div class="info-row">
                        <span>Status</span>
                        <strong><?= $lab['status'] === 'active' ? 'Tersedia' : 'Nonaktif' ?></strong>
                    </div>
                    <div class="info-row">
                        <span>Total booking</span>
                        <strong><?= $totalBookings ?> booking</strong>
                    </div>
                    <div class="info-row">
                        <span>Booking selesai</span>
                        <strong><?= $completedBookings ?> booking</strong>
                    </div>
                    <div class="info-row">
                        <span>Jadwal mendatang</span>
                        <strong><?= count($upcoming) ?> agenda</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-body">
                <div class="summary-lab">
                    <span class="summary-lab-mark"><?= icon(lab_icon_name($lab)) ?></span>
                    <div>
                        <strong><?= e($lab['name']) ?></strong>
                        <span><?= e($lab['location']) ?></span>
                    </div>
                </div>

                <?php if ($lab['status'] === 'active'): ?>
                    <div class="summary-list">
                        <div class="summary-row">
                            <span><?= icon('clock') ?>Jam operasional</span>
                            <strong>07.00 – 22.00</strong>
                        </div>
                        <div class="summary-row">
                            <span><?= icon('users') ?>Kapasitas</span>
                            <strong><?= (int) $lab['capacity'] ?> orang</strong>
                        </div>
                    </div>

                    <div class="booking-note" style="margin-bottom:13px">
                        <?= icon('info') ?>
                        <span>Ajukan booking minimal satu hari sebelumnya agar dapat ditinjau oleh admin.</span>
                    </div>

                    <?php if ($isAdmin): ?>
                        <a class="btn btn-primary btn-block" href="<?= e(url('labs/edit.php?id=' . (int) $lab['id'])) ?>"><?= icon('edit') ?>Perbarui Data Lab</a>
                    <?php else: ?>
                        <a class="btn btn-primary btn-block" href="<?= e(url('bookings/create.php?lab_id=' . (int) $lab['id'])) ?>"><?= icon('calendar-check') ?>Ajukan Booking</a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <?= icon('alert-triangle') ?>
                        <span>Laboratorium ini sedang nonaktif sehingga belum dapat dipesan. Silakan pilih laboratorium lain.</span>
                    </div>
                    <a class="btn btn-secondary btn-block" style="margin-top:12px" href="<?= e(url('labs/index.php')) ?>">Lihat lab lainnya</a>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

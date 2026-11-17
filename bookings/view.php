<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_login();

$user = current_user();
$isAdmin = $user['role'] === 'admin';
$bookingId = (int) get('id');

if ($bookingId <= 0) {
    flash('danger', 'Booking yang Anda cari tidak ditemukan.');
    redirect('bookings/index.php');
}

$booking = q(
    'SELECT b.*,
            l.name AS lab_name, l.code AS lab_code, l.location AS lab_location,
            l.capacity AS lab_capacity, l.status AS lab_status,
            u.name AS user_name, u.email AS user_email, u.phone AS user_phone, u.department AS user_department,
            r.name AS reviewer_name
     FROM bookings b
     JOIN labs l ON l.id = b.lab_id
     JOIN users u ON u.id = b.user_id
     LEFT JOIN users r ON r.id = b.reviewed_by
     WHERE b.id = ?
     LIMIT 1',
    [$bookingId]
)->fetch();

if (!$booking) {
    flash('danger', 'Booking yang Anda cari tidak ditemukan.');
    redirect('bookings/index.php');
}

if (!$isAdmin && (int) $booking['user_id'] !== (int) $user['id']) {
    flash('danger', 'Anda tidak memiliki akses untuk melihat booking tersebut.');
    redirect('bookings/index.php');
}

$statusMeta = [
    'pending'   => ['class' => 'is-warning', 'icon' => 'clock', 'title' => 'Menunggu persetujuan admin', 'text' => 'Pengajuan Anda sedang ditinjau oleh admin laboratorium.'],
    'approved'  => ['class' => 'is-success', 'icon' => 'check-circle', 'title' => 'Booking disetujui', 'text' => 'Booking telah disetujui. Silakan gunakan laboratorium sesuai jadwal.'],
    'rejected'  => ['class' => 'is-danger', 'icon' => 'x-circle', 'title' => 'Booking ditolak', 'text' => 'Pengajuan tidak disetujui. Periksa catatan admin untuk informasi lanjutan.'],
    'cancelled' => ['class' => 'is-neutral', 'icon' => 'slash', 'title' => 'Booking dibatalkan', 'text' => 'Booking ini telah dibatalkan dan tidak lagi menjadi bagian dari jadwal aktif.'],
    'completed' => ['class' => 'is-info', 'icon' => 'flag', 'title' => 'Kegiatan selesai', 'text' => 'Kegiatan telah selesai dilaksanakan di laboratorium.'],
];
$currentStatusMeta = $statusMeta[$booking['status']] ?? $statusMeta['pending'];

$durationMinutes = minutes_between($booking['start_time'], $booking['end_time']);
$endTimestamp = strtotime($booking['booking_date'] . ' ' . $booking['end_time']);
$isPast = $endTimestamp !== false && $endTimestamp <= time();
$canCancel = in_array($booking['status'], ['pending', 'approved'], true)
    && !$isPast
    && ($isAdmin || (int) $booking['user_id'] === (int) $user['id']);
$approvalConflict = $booking['status'] === 'pending'
    && has_booking_conflict(
        (int) $booking['lab_id'],
        $booking['booking_date'],
        $booking['start_time'],
        $booking['end_time'],
        ['approved'],
        (int) $booking['id']
    );
$canApprove = $booking['status'] === 'pending' && !$isPast && !$approvalConflict;

$timeline = [];
$timeline[] = [
    'label' => 'Booking diajukan',
    'time'  => format_datetime($booking['created_at']),
    'state' => 'is-done',
];

if ($booking['status'] === 'pending') {
    $timeline[] = [
        'label' => 'Menunggu persetujuan admin',
        'time'  => 'Sedang ditinjau',
        'state' => 'is-current',
    ];
} elseif ($booking['status'] === 'rejected') {
    $timeline[] = [
        'label' => 'Ditolak oleh ' . ($booking['reviewer_name'] ?: 'admin'),
        'time'  => format_datetime($booking['reviewed_at']),
        'state' => 'is-done',
    ];
} elseif ($booking['status'] === 'cancelled') {
    $timeline[] = [
        'label' => 'Booking dibatalkan',
        'time'  => format_datetime($booking['updated_at']),
        'state' => 'is-done',
    ];
} else {
    $timeline[] = [
        'label' => 'Disetujui oleh ' . ($booking['reviewer_name'] ?: 'admin'),
        'time'  => format_datetime($booking['reviewed_at']),
        'state' => 'is-done',
    ];

    if ($booking['status'] === 'completed') {
        $timeline[] = [
            'label' => 'Kegiatan selesai',
            'time'  => format_date($booking['booking_date'], 'short', true) . ' · ' . format_time($booking['end_time']),
            'state' => 'is-done',
        ];
    } else {
        $timeline[] = [
            'label' => 'Kegiatan dijadwalkan',
            'time'  => format_date($booking['booking_date'], 'short', true) . ' · ' . format_time_range($booking['start_time'], $booking['end_time']),
            'state' => 'is-current',
        ];
    }
}

$pageTitle = 'Detail Booking';
$active = 'bookings';

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('clipboard') ?>Detail Booking</span>
        <h1 class="page-title">Detail Booking</h1>
        <p class="page-subtitle">
            <?= e($booking['booking_code']) ?> · Diajukan oleh <?= e($booking['user_name']) ?> pada <?= e(format_datetime($booking['created_at'])) ?>.
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('bookings/index.php')) ?>"><?= icon('arrow-left') ?>Kembali</a>
        <button class="btn btn-secondary" type="button" data-print><?= icon('printer') ?>Cetak</button>
    </div>
</div>

<div class="status-banner <?= e($currentStatusMeta['class']) ?>">
    <span class="status-banner-icon"><?= icon($currentStatusMeta['icon']) ?></span>
    <div class="status-banner-main">
        <strong><?= e($currentStatusMeta['title']) ?></strong>
        <p><?= e($currentStatusMeta['text']) ?></p>
    </div>
    <span class="status-banner-code"><?= e($booking['booking_code']) ?></span>
</div>

<div class="detail-grid">
    <div class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Informasi Booking</h2>
                    <p class="card-subtitle">Rincian jadwal dan kegiatan yang diajukan.</p>
                </div>
                <?= booking_status_badge($booking['status']) ?>
            </div>
            <div class="card-body">
                <dl class="detail-list">
                    <div class="detail-list-row">
                        <dt>Laboratorium</dt>
                        <dd><?= e($booking['lab_name']) ?> (<?= e($booking['lab_code']) ?>)</dd>
                    </div>
                    <div class="detail-list-row">
                        <dt>Lokasi</dt>
                        <dd><?= e($booking['lab_location']) ?></dd>
                    </div>
                    <div class="detail-list-row">
                        <dt>Tanggal</dt>
                        <dd><?= e(format_date($booking['booking_date'], 'long', true)) ?></dd>
                    </div>
                    <div class="detail-list-row">
                        <dt>Waktu</dt>
                        <dd><?= e(format_time_range($booking['start_time'], $booking['end_time'])) ?></dd>
                    </div>
                    <div class="detail-list-row">
                        <dt>Durasi</dt>
                        <dd><?= e(format_duration($durationMinutes)) ?></dd>
                    </div>
                    <div class="detail-list-row">
                        <dt>Jumlah peserta</dt>
                        <dd><?= (int) $booking['participants'] ?> orang</dd>
                    </div>
                    <div class="detail-list-row">
                        <dt>Peminjam</dt>
                        <dd><?= e($booking['user_name']) ?></dd>
                    </div>
                </dl>

                <div class="form-section" style="margin-top:20px">
                    <div class="form-section-title"><?= icon('book') ?>Tujuan kegiatan</div>
                    <p class="prose-block"><?= e($booking['purpose']) ?></p>
                </div>
            </div>
        </section>

        <?php if (!empty($booking['admin_note'])): ?>
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Catatan Admin</h2>
                        <p class="card-subtitle">Pesan dari pengelola laboratorium.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert <?= $booking['status'] === 'rejected' ? 'alert-danger' : 'alert-info' ?>">
                        <?= icon($booking['status'] === 'rejected' ? 'alert-circle' : 'info') ?>
                        <span><?= nl2br(e($booking['admin_note'])) ?></span>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Riwayat Status</h2>
                    <p class="card-subtitle">Perjalanan pengajuan booking Anda.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($timeline as $step): ?>
                        <div class="timeline-item <?= e($step['state']) ?>">
                            <span class="timeline-dot"></span>
                            <div class="timeline-content">
                                <strong><?= e($step['label']) ?></strong>
                                <span><?= e($step['time']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <?php if ($isAdmin && $booking['status'] === 'pending'): ?>
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Tinjau Pengajuan</h2>
                        <p class="card-subtitle">Berikan keputusan untuk booking ini.</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($approvalConflict): ?>
                        <div class="alert alert-warning" style="margin-bottom:14px">
                            <?= icon('alert-triangle') ?>
                            <span>Jadwal ini bertabrakan dengan booking lain yang sudah disetujui. Booking tidak dapat disetujui pada waktu tersebut.</span>
                        </div>
                    <?php elseif ($isPast): ?>
                        <div class="alert alert-warning" style="margin-bottom:14px">
                            <?= icon('alert-triangle') ?>
                            <span>Waktu booking sudah berlalu sehingga tidak dapat disetujui. Anda masih dapat menolak pengajuan ini.</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($canApprove): ?>
                        <form method="post" action="<?= e(url('bookings/action.php')) ?>" data-submit-once>
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button class="btn btn-primary btn-block" type="submit"><?= icon('check-circle') ?>Setujui Booking</button>
                        </form>
                    <?php endif; ?>

                    <div class="approval-box">
                        <form class="reject-form" method="post" action="<?= e(url('bookings/action.php')) ?>" data-submit-once>
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <label class="field-label" for="admin_note">Alasan penolakan <span class="req">*</span></label>
                            <textarea class="textarea" id="admin_note" name="admin_note" placeholder="Tuliskan alasan agar pemohon dapat memahami keputusan…" minlength="5" maxlength="1000" required></textarea>
                            <button class="btn btn-danger-soft btn-block" type="submit"><?= icon('x-circle') ?>Tolak Pengajuan</button>
                        </form>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Laboratorium</h2>
                    <p class="card-subtitle">Informasi ruang yang dipesan.</p>
                </div>
                <?= lab_status_badge($booking['lab_status']) ?>
            </div>
            <div class="card-body">
                <div class="summary-lab">
                    <span class="summary-lab-mark"><?= icon(lab_icon_name(['code' => $booking['lab_code'], 'name' => $booking['lab_name']])) ?></span>
                    <div>
                        <strong><?= e($booking['lab_name']) ?></strong>
                        <span><?= e($booking['lab_code']) ?> · <?= e($booking['lab_location']) ?></span>
                    </div>
                </div>
                <div class="summary-list">
                    <div class="summary-row">
                        <span><?= icon('users') ?>Kapasitas</span>
                        <strong><?= (int) $booking['lab_capacity'] ?> orang</strong>
                    </div>
                    <div class="summary-row">
                        <span><?= icon('calendar') ?>Tanggal</span>
                        <strong><?= e(format_date($booking['booking_date'])) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span><?= icon('clock') ?>Waktu</span>
                        <strong><?= e(format_time_range($booking['start_time'], $booking['end_time'])) ?></strong>
                    </div>
                </div>
                <a class="btn btn-secondary btn-block" style="margin-top:15px" href="<?= e(url('labs/view.php?id=' . (int) $booking['lab_id'])) ?>">
                    <?= icon('flask') ?>Lihat Detail Lab
                </a>
            </div>
        </section>

        <?php if ($isAdmin): ?>
            <section class="card">
                <div class="card-header">
                    <div class="card-heading">
                        <h2 class="card-title">Peminjam</h2>
                        <p class="card-subtitle">Data pengguna yang mengajukan.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary-lab">
                        <?= avatar($booking['user_name']) ?>
                        <div>
                            <strong><?= e($booking['user_name']) ?></strong>
                            <span><?= e($booking['user_department'] ?: 'Pengguna LAB BOOKING') ?></span>
                        </div>
                    </div>
                    <div class="summary-list">
                        <div class="summary-row">
                            <span><?= icon('mail') ?>Email</span>
                            <strong><?= e($booking['user_email']) ?></strong>
                        </div>
                        <div class="summary-row">
                            <span><?= icon('phone') ?>Telepon</span>
                            <strong><?= e($booking['user_phone'] ?: '-') ?></strong>
                        </div>
                        <div class="summary-row">
                            <span><?= icon('users') ?>Peserta</span>
                            <strong><?= (int) $booking['participants'] ?> orang</strong>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($canCancel): ?>
            <section class="card">
                <div class="card-body">
                    <h2 class="card-title" style="font-size:12.5px">Batalkan booking</h2>
                    <p class="inline-note" style="margin-bottom:10px">
                        Booking yang dibatalkan akan dilepas dari jadwal dan tidak dapat dikembalikan.
                    </p>
                    <form method="post" action="<?= e(url('bookings/action.php')) ?>" data-confirm="Batalkan booking <?= e($booking['booking_code']) ?>? Tindakan ini tidak dapat dibatalkan.">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>">
                        <input type="hidden" name="action" value="cancel">
                        <button class="btn btn-danger-soft btn-block" type="submit"><?= icon('x') ?>Batalkan Booking</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <div class="alert alert-info">
            <?= icon('info') ?>
            <span>Simpan kode booking <strong><?= e($booking['booking_code']) ?></strong> untuk memudahkan penelusuran saat menghubungi admin.</span>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

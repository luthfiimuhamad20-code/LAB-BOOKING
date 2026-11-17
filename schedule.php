<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$isAdmin = $user['role'] === 'admin';

$pageTitle = 'Jadwal Lab';
$active = 'schedule';

$date = get('date');

if ($date === '' || !is_valid_date($date)) {
    $date = date('Y-m-d');
}

$labFilter = (int) get('lab_id');
$labs = q('SELECT id, name, code FROM labs ORDER BY name ASC')->fetchAll();

$statuses = $isAdmin ? ['pending', 'approved', 'completed'] : ['approved', 'completed'];
$placeholders = implode(',', array_fill(0, count($statuses), '?'));

$sql = "SELECT b.*, l.name AS lab_name, l.code AS lab_code, l.location AS lab_location,
               u.name AS user_name
        FROM bookings b
        JOIN labs l ON l.id = b.lab_id
        JOIN users u ON u.id = b.user_id
        WHERE b.booking_date = ?
          AND b.status IN ($placeholders)";
$parameters = array_merge([$date], $statuses);

if ($labFilter > 0) {
    $sql .= ' AND b.lab_id = ?';
    $parameters[] = $labFilter;
}

$sql .= ' ORDER BY b.start_time ASC, l.name ASC';
$agenda = q($sql, $parameters)->fetchAll();

$totalMinutes = 0;
$usedLabs = [];
foreach ($agenda as $item) {
    $totalMinutes += minutes_between($item['start_time'], $item['end_time']);
    $usedLabs[$item['lab_id']] = true;
}

$selectedDate = new DateTimeImmutable($date);
$today = new DateTimeImmutable('today');
$previousDate = $selectedDate->modify('-1 day')->format('Y-m-d');
$nextDate = $selectedDate->modify('+1 day')->format('Y-m-d');

$scheduleUrl = static function (string $targetDate) use ($labFilter): string {
    $query = ['date' => $targetDate];
    if ($labFilter > 0) {
        $query['lab_id'] = $labFilter;
    }

    return url('schedule.php') . '?' . http_build_query($query);
};

require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('calendar') ?>Agenda Laboratorium</span>
        <h1 class="page-title">Jadwal Lab</h1>
        <p class="page-subtitle">
            <?= $isAdmin
                ? 'Pantau seluruh agenda pemakaian laboratorium, termasuk pengajuan yang masih menunggu.'
                : 'Lihat jadwal pemakaian laboratorium yang telah disetujui.' ?>
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="<?= e(url('bookings/create.php' . ($labFilter > 0 ? '?lab_id=' . $labFilter : ''))) ?>">
            <?= icon('plus') ?>Buat Booking
        </a>
    </div>
</div>

<div class="date-toolbar">
    <div class="date-nav">
        <a class="btn btn-secondary btn-icon" href="<?= e($scheduleUrl($previousDate)) ?>" aria-label="Hari sebelumnya">
            <?= icon('chevron-left') ?>
        </a>
        <div class="date-display">
            <?= e(format_date($date, 'long', true)) ?>
            <span><?= $selectedDate->format('Y-m-d') === $today->format('Y-m-d') ? 'Hari ini' : 'Jadwal harian' ?></span>
        </div>
        <a class="btn btn-secondary btn-icon" href="<?= e($scheduleUrl($nextDate)) ?>" aria-label="Hari berikutnya">
            <?= icon('chevron-right') ?>
        </a>
        <?php if ($date !== $today->format('Y-m-d')): ?>
            <a class="btn btn-sm btn-soft" href="<?= e($scheduleUrl($today->format('Y-m-d'))) ?>">Hari ini</a>
        <?php endif; ?>
    </div>

    <form class="date-picker-form" method="get" action="<?= e(url('schedule.php')) ?>">
        <input class="input" type="date" name="date" value="<?= e($date) ?>" aria-label="Pilih tanggal jadwal">
        <select class="select" name="lab_id" aria-label="Filter laboratorium">
            <option value="">Semua lab</option>
            <?php foreach ($labs as $lab): ?>
                <option value="<?= (int) $lab['id'] ?>"<?= selected($labFilter === (int) $lab['id']) ?>>
                    <?= e($lab['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-secondary" type="submit"><?= icon('filter') ?>Tampilkan</button>
    </form>
</div>

<div class="schedule-summary">
    <span><?= icon('calendar-check') ?><?= count($agenda) ?> agenda terjadwal</span>
    <span><?= icon('flask') ?><?= count($usedLabs) ?> laboratorium digunakan</span>
    <span><?= icon('clock') ?><?= e(format_duration($totalMinutes)) ?> total durasi</span>
</div>

<section class="card">
    <div class="card-header">
        <div class="card-heading">
            <h2 class="card-title">Agenda <?= e(format_date($date, 'long', true)) ?></h2>
            <p class="card-subtitle">
                <?= $isAdmin ? 'Termasuk pengajuan berstatus menunggu persetujuan.' : 'Hanya menampilkan jadwal yang sudah disetujui.' ?>
            </p>
        </div>
        <span class="badge badge-primary"><?= count($agenda) ?> booking</span>
    </div>

    <?php if ($agenda === []): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= icon('calendar') ?></div>
            <h3>Tidak ada jadwal pada tanggal ini</h3>
            <p>
                <?= $labFilter > 0
                    ? 'Laboratorium yang dipilih belum memiliki agenda pada tanggal tersebut. Coba pilih lab atau tanggal lain.'
                    : 'Belum ada pemakaian laboratorium yang terjadwal. Anda dapat menjadi yang pertama mengajukan booking.' ?>
            </p>
            <a class="btn btn-primary btn-sm" href="<?= e(url('bookings/create.php' . ($labFilter > 0 ? '?lab_id=' . $labFilter : ''))) ?>">
                <?= icon('plus') ?>Buat Booking
            </a>
        </div>
    <?php else: ?>
        <div class="schedule-list">
            <?php foreach ($agenda as $item): ?>
                <div class="schedule-row">
                    <div class="schedule-row-time<?= $item['status'] === 'pending' ? ' is-pending' : '' ?>">
                        <strong><?= e(format_time($item['start_time'])) ?></strong>
                        <span>s.d. <?= e(format_time($item['end_time'])) ?></span>
                    </div>
                    <div class="schedule-row-main">
                        <strong><?= e($item['purpose']) ?></strong>
                        <p><?= icon('user') ?> <?= e($item['user_name']) ?> · <?= (int) $item['participants'] ?> peserta</p>
                    </div>
                    <div class="schedule-row-meta">
                        <span class="lab-label">
                            <span class="schedule-lab-icon"><?= icon(lab_icon_name(['code' => $item['lab_code'], 'name' => $item['lab_name']])) ?></span>
                            <?= e($item['lab_name']) ?>
                        </span>
                        <?= booking_status_badge($item['status']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if (!$isAdmin): ?>
    <p class="inline-note">
        <?= icon('info') ?> Jadwal hanya menampilkan booking yang telah disetujui admin. Pengajuan yang masih menunggu tidak ditampilkan kepada pengguna lain.
    </p>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

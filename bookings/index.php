<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_login();

$user = current_user();
$isAdmin = $user['role'] === 'admin';

$pageTitle = $isAdmin ? 'Semua Booking' : 'Booking Saya';
$active = 'bookings';

$statuses = booking_statuses();
$statusFilter = get('status');
if ($statusFilter !== '' && !isset($statuses[$statusFilter])) {
    $statusFilter = '';
}

$search = get('q');
$labFilter = (int) get('lab_id');
$page = max(1, (int) get('page', '1'));
$perPage = 10;

$from = 'FROM bookings b
         JOIN labs l ON l.id = b.lab_id
         JOIN users u ON u.id = b.user_id';
$conditions = [];
$parameters = [];

if (!$isAdmin) {
    $conditions[] = 'b.user_id = ?';
    $parameters[] = (int) $user['id'];
}

if ($statusFilter !== '') {
    $conditions[] = 'b.status = ?';
    $parameters[] = $statusFilter;
}

if ($labFilter > 0) {
    $conditions[] = 'b.lab_id = ?';
    $parameters[] = $labFilter;
}

if ($search !== '') {
    $searchFields = ['b.booking_code LIKE ?', 'b.purpose LIKE ?', 'l.name LIKE ?'];
    $parameters[] = '%' . $search . '%';
    $parameters[] = '%' . $search . '%';
    $parameters[] = '%' . $search . '%';

    if ($isAdmin) {
        $searchFields[] = 'u.name LIKE ?';
        $searchFields[] = 'u.email LIKE ?';
        $parameters[] = '%' . $search . '%';
        $parameters[] = '%' . $search . '%';
    }

    $conditions[] = '(' . implode(' OR ', $searchFields) . ')';
}

$where = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';
$total = (int) q('SELECT COUNT(*) ' . $from . ' ' . $where, $parameters)->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$order = $isAdmin
    ? "ORDER BY FIELD(b.status, 'pending', 'approved', 'completed', 'rejected', 'cancelled'), b.booking_date DESC, b.start_time DESC"
    : 'ORDER BY b.booking_date DESC, b.start_time DESC';

$bookings = q(
    'SELECT b.*, l.name AS lab_name, l.code AS lab_code, l.location AS lab_location,
            u.name AS user_name, u.email AS user_email, u.department AS user_department
     ' . $from . ' ' . $where . ' ' . $order . ' LIMIT ' . $perPage . ' OFFSET ' . $offset,
    $parameters
)->fetchAll();

$countParameters = $isAdmin ? [] : [(int) $user['id']];
$countRows = q(
    'SELECT b.status, COUNT(*) AS total FROM bookings b ' . ($isAdmin ? '' : 'WHERE b.user_id = ?') . ' GROUP BY b.status',
    $countParameters
)->fetchAll();

$statusCounts = array_fill_keys(array_keys($statuses), 0);
foreach ($countRows as $row) {
    if (isset($statusCounts[$row['status']])) {
        $statusCounts[$row['status']] = (int) $row['total'];
    }
}
$totalAll = array_sum($statusCounts);
$activeTotal = (int) q(
    "SELECT COUNT(*) FROM bookings WHERE status IN ('pending', 'approved')" . ($isAdmin ? '' : ' AND user_id = ?'),
    $isAdmin ? [] : [(int) $user['id']]
)->fetchColumn();

$filterQuery = [];
if ($search !== '') {
    $filterQuery['q'] = $search;
}
if ($labFilter > 0) {
    $filterQuery['lab_id'] = $labFilter;
}

$tabQuery = $filterQuery;
$tabUrl = static function (?string $statusKey) use ($tabQuery): string {
    $query = $tabQuery;
    if ($statusKey !== null) {
        $query['status'] = $statusKey;
    }

    return url('bookings/index.php') . ($query !== [] ? '?' . http_build_query($query) : '');
};

$labOptions = q('SELECT id, name, code FROM labs ORDER BY name ASC')->fetchAll();
$paginationQuery = $filterQuery;
if ($statusFilter !== '') {
    $paginationQuery['status'] = $statusFilter;
}

$hasFilter = $search !== '' || $labFilter > 0 || $statusFilter !== '';

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('calendar-check') ?>Manajemen Booking</span>
        <h1 class="page-title"><?= $isAdmin ? 'Semua Booking' : 'Booking Saya' ?></h1>
        <p class="page-subtitle">
            <?= $isAdmin
                ? 'Tinjau, setujui, dan pantau seluruh pengajuan pemakaian laboratorium.'
                : 'Pantau status dan riwayat pengajuan pemakaian laboratorium Anda.' ?>
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="<?= e(url('bookings/create.php')) ?>"><?= icon('plus') ?>Buat Booking</a>
    </div>
</div>

<div class="status-tabs" style="margin-bottom:12px" aria-label="Filter status booking">
    <a class="status-tab<?= $statusFilter === '' ? ' is-active' : '' ?>" href="<?= e($tabUrl(null)) ?>">
        Semua <span class="status-tab-count"><?= $totalAll ?></span>
    </a>
    <?php foreach ($statuses as $statusKey => $statusMeta): ?>
        <a class="status-tab<?= $statusFilter === $statusKey ? ' is-active' : '' ?>" href="<?= e($tabUrl($statusKey)) ?>">
            <?= e($statusMeta['label']) ?> <span class="status-tab-count"><?= $statusCounts[$statusKey] ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="filter-bar">
    <form class="filter-form" method="get" action="<?= e(url('bookings/index.php')) ?>">
        <?php if ($statusFilter !== ''): ?>
            <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
        <?php endif; ?>
        <div class="filter-search">
            <?= icon('search') ?>
            <input class="input" type="search" name="q" value="<?= e($search) ?>"
                   placeholder="<?= $isAdmin ? 'Cari kode, peminjam, atau tujuan…' : 'Cari kode booking atau tujuan…' ?>"
                   aria-label="Cari booking">
        </div>
        <select class="select" name="lab_id" aria-label="Filter laboratorium" data-auto-submit>
            <option value="">Semua laboratorium</option>
            <?php foreach ($labOptions as $labOption): ?>
                <option value="<?= (int) $labOption['id'] ?>"<?= selected($labFilter === (int) $labOption['id']) ?>>
                    <?= e($labOption['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-secondary" type="submit"><?= icon('filter') ?>Terapkan</button>
        <?php if ($hasFilter): ?>
            <a class="filter-reset" href="<?= e(url('bookings/index.php')) ?>"><?= icon('x') ?>Reset</a>
        <?php endif; ?>
    </form>
</div>

<section class="card">
    <?php if ($bookings === []): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= icon('calendar') ?></div>
            <h3><?= $hasFilter ? 'Booking tidak ditemukan' : 'Belum ada booking' ?></h3>
            <p>
                <?= $hasFilter
                    ? 'Tidak ada booking yang cocok dengan pencarian atau filter yang Anda gunakan.'
                    : ($isAdmin
                        ? 'Belum ada pengajuan booking yang masuk ke LAB BOOKING.'
                        : 'Anda belum pernah mengajukan booking laboratorium. Mulai dengan membuat pengajuan baru.') ?>
            </p>
            <?php if ($hasFilter): ?>
                <a class="btn btn-secondary btn-sm" href="<?= e(url('bookings/index.php')) ?>">Tampilkan semua booking</a>
            <?php elseif (!$isAdmin): ?>
                <a class="btn btn-primary btn-sm" href="<?= e(url('bookings/create.php')) ?>"><?= icon('plus') ?>Buat Booking</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <?php if ($isAdmin): ?>
                            <th>Peminjam</th>
                        <?php endif; ?>
                        <th>Laboratorium</th>
                        <th>Jadwal</th>
                        <?php if (!$isAdmin): ?>
                            <th>Peserta</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>
                                <a class="table-code" href="<?= e(url('bookings/view.php?id=' . (int) $booking['id'])) ?>">
                                    <?= e($booking['booking_code']) ?>
                                </a>
                                <span class="cell-sub"><?= e(format_date($booking['created_at'])) ?></span>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <div class="table-user">
                                        <?= avatar($booking['user_name'], 'avatar-sm') ?>
                                        <div class="table-user-info">
                                            <strong><?= e($booking['user_name']) ?></strong>
                                            <span><?= e($booking['user_department'] ?: $booking['user_email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td>
                                <span class="cell-main"><?= e($booking['lab_name']) ?></span>
                                <span class="cell-sub"><?= e($booking['lab_code']) ?> · <?= e($booking['lab_location']) ?></span>
                            </td>
                            <td>
                                <div class="schedule-cell">
                                    <strong><?= e(format_date($booking['booking_date'])) ?></strong>
                                    <span><?= e(format_time_range($booking['start_time'], $booking['end_time'])) ?></span>
                                </div>
                            </td>
                            <?php if (!$isAdmin): ?>
                                <td><?= (int) $booking['participants'] ?> orang</td>
                            <?php endif; ?>
                            <td><?= booking_status_badge($booking['status']) ?></td>
                            <td>
                                <div class="table-actions">
                                    <?php if ($isAdmin && $booking['status'] === 'pending'): ?>
                                        <form method="post" action="<?= e(url('bookings/action.php')) ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button class="btn btn-icon btn-sm btn-success" type="submit" title="Setujui booking" aria-label="Setujui booking <?= e($booking['booking_code']) ?>">
                                                <?= icon('check') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$isAdmin && in_array($booking['status'], ['pending', 'approved'], true) && $booking['booking_date'] >= date('Y-m-d')): ?>
                                        <form method="post" action="<?= e(url('bookings/action.php')) ?>" data-confirm="Batalkan booking <?= e($booking['booking_code']) ?>?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>">
                                            <input type="hidden" name="action" value="cancel">
                                            <button class="btn btn-icon btn-sm btn-danger-soft" type="submit" title="Batalkan booking" aria-label="Batalkan booking <?= e($booking['booking_code']) ?>">
                                                <?= icon('x') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <a class="btn btn-sm btn-secondary" href="<?= e(url('bookings/view.php?id=' . (int) $booking['id'])) ?>">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span><?= e(pagination_info($total, $page, $perPage)) ?></span>
            <?= pagination($total, $page, $perPage, 'bookings/index.php', $paginationQuery) ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($isAdmin && $activeTotal > 0): ?>
    <p class="inline-note">
        <?= icon('info') ?> Ada <?= $activeTotal ?> booking aktif (menunggu atau disetujui) di LAB BOOKING saat ini.
    </p>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
